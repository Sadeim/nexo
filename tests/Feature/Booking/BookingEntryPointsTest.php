<?php

namespace Tests\Feature\Booking;

use App\Models\Employee;
use Database\Seeders\DatabaseSeeder;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The booking funnel's wiring, asserted on rendered HTML.
 *
 * On 9 Nov 2026 every Book Now control on the live site was inert. The page
 * was a clean 200 and the markup was perfect; the script that binds the
 * buttons was a 404, because the document root is a sibling of the app and
 * nothing copied public/frontend_assets/js/booking.js into it.
 *
 * Three things have to hold for a Book Now button to do anything, and a test
 * that checks only the first two is the test we did not have:
 *
 *   1. the modal is in the DOM,
 *   2. booking.js is requested, after the modal,
 *   3. every control carries the one selector booking.js delegates on.
 *
 * (3) is the part no smoke test can see. booking.js binds a single delegated
 * listener for `.book-now-btn`, so a new button added without that class is
 * dead on arrival and looks entirely correct in review. These tests fail if
 * anyone adds one.
 *
 * Whether the file is actually *served* is a deployment property, not a code
 * one — deploy/deploy.sh checks that against the live site.
 */
class BookingEntryPointsTest extends TestCase
{
    use RefreshDatabase;

    /** The only selector booking.js binds to. Changing it here is a no-op; change it there too. */
    private const TRIGGER_CLASS = 'book-now-btn';

    private const SCRIPT_PATH = 'frontend_assets/js/booking.js';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    // ---------- the three preconditions, on the homepage ----------

    public function test_the_homepage_renders_the_booking_modal(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="booking-modal"', false);
    }

    public function test_the_homepage_requests_the_booking_script(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(self::SCRIPT_PATH, false);
    }

    /**
     * booking.js reads getElementById('booking-modal') at the top level and
     * returns early when it is null. A script tag moved above the modal — or
     * into the <head> — disables booking without changing a line of JS.
     */
    public function test_the_booking_script_is_requested_after_the_modal(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $modalAt  = strpos($html, 'id="booking-modal"');
        $scriptAt = strpos($html, self::SCRIPT_PATH);

        $this->assertNotFalse($modalAt, 'The booking modal is not in the homepage at all.');
        $this->assertNotFalse($scriptAt, 'booking.js is not requested by the homepage at all.');
        $this->assertLessThan(
            $scriptAt,
            $modalAt,
            'booking.js is requested before the modal exists, so it will bind nothing.'
        );
    }

    /**
     * The asset has to exist in the repository under the name the page asks
     * for. This catches a rename or a move; it cannot catch the document root
     * not having a copy, which is deploy.sh's job.
     */
    public function test_the_booking_script_exists_in_the_repository(): void
    {
        $this->assertFileExists(
            public_path(self::SCRIPT_PATH),
            self::SCRIPT_PATH.' is referenced by the homepage but is not in public/.'
        );
    }

    // ---------- every control carries the selector ----------

    public function test_every_book_now_control_on_the_homepage_carries_the_trigger_class(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $controls = $this->bookingControls($html);

        $this->assertNotEmpty($controls, 'Found no Book Now control on the homepage to check.');

        foreach ($controls as $control) {
            $this->assertStringContainsString(
                self::TRIGGER_CLASS,
                $control['class'],
                sprintf(
                    'The <%s> reading "%s" is a booking control with no %s class, '
                    .'so booking.js will never open the modal for it.',
                    $control['tag'],
                    $control['text'],
                    self::TRIGGER_CLASS
                )
            );
        }

        $this->assertStringContainsString($barber->name, $html);
    }

    public function test_every_book_now_control_on_a_barber_page_carries_the_trigger_class(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $html = $this->get(route('barbers.show', $barber->slug))->assertOk()->getContent();

        $controls = $this->bookingControls($html);

        $this->assertNotEmpty($controls, 'Found no Book Now control on the barber page to check.');

        foreach ($controls as $control) {
            $this->assertStringContainsString(
                self::TRIGGER_CLASS,
                $control['class'],
                sprintf(
                    'The <%s> reading "%s" on the barber page has no %s class.',
                    $control['tag'],
                    $control['text'],
                    self::TRIGGER_CLASS
                )
            );
        }
    }

    /**
     * A card's button hands booking.js the barber to lock the form to. Without
     * the data attributes the modal opens unfiltered and the customer silently
     * books "any barber" from a page about one person.
     */
    public function test_a_barber_control_carries_the_barber_it_books(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $this->get(route('barbers.show', $barber->slug))
            ->assertOk()
            ->assertSee('data-barber-id="'.$barber->id.'"', false)
            ->assertSee('data-barber-name="'.$barber->name.'"', false)
            ->assertSee('Book with '.$barber->first_name);
    }

    // ---------- the source, not just what rendered ----------

    /**
     * Some controls only render under conditions a seeded test does not meet:
     * the header has two variants and only one is used per route, and the hero
     * button depends on CMS rows. Those are exactly the controls a rendered
     * assertion cannot see, so check the templates themselves as well.
     *
     * For every call to action in the frontend templates, the element opening
     * it must carry the trigger class. The modal's own submit button is the
     * end of the funnel and is excluded.
     */
    public function test_every_book_now_control_in_the_templates_carries_the_trigger_class(): void
    {
        $offenders = [];
        $checked   = 0;

        foreach ($this->frontendTemplates() as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);

            foreach ($lines as $i => $line) {
                if (! preg_match('/\bBOOK NOW\b|\bBook with\b/i', $line)) {
                    continue;
                }

                // Walk back to the element that opens this control.
                $openTag = null;
                for ($j = $i; $j >= max(0, $i - 15); $j--) {
                    if (preg_match('/<(button|a)\b/i', $lines[$j])) {
                        $openTag = implode(' ', array_slice($lines, $j, $i - $j + 1));
                        break;
                    }
                }

                if ($openTag === null) {
                    continue;
                }

                $checked++;

                if (! str_contains($openTag, self::TRIGGER_CLASS)) {
                    $offenders[] = sprintf(
                        '%s:%d', str_replace(base_path().'/', '', $file), $i + 1
                    );
                }
            }
        }

        $this->assertGreaterThanOrEqual(
            5,
            $checked,
            'Expected to find at least the five known Book Now controls in the templates; '
            .'found '.$checked.'. The matcher has probably gone stale.'
        );

        $this->assertSame(
            [],
            $offenders,
            "These booking controls have no ".self::TRIGGER_CLASS." class, so booking.js\n"
            ."will never open the modal for them:\n  ".implode("\n  ", $offenders)
        );
    }

    /**
     * The last gap, and the reason this test is a count.
     *
     * The hero button's label is {{ $slider->button_text }} — it comes out of
     * the CMS. No matcher reading the template or the rendered page can tell
     * that button apart from any other, so neither test above can protect it.
     *
     * What is still checkable is the inventory: how many booking triggers each
     * template is supposed to contain. Deleting the class from any control in
     * any of these files moves a count and fails here with the file named.
     *
     * Adding or removing a control is a legitimate reason to edit this map.
     * Doing so is the moment to check the new control carries the class.
     */
    public function test_the_templates_contain_the_expected_booking_triggers(): void
    {
        $expected = [
            'home.blade.php'            => 3,  // hero, about section, barber card
            'partials/header.blade.php' => 3,  // home variant, inner-page variant, mobile menu
            'barber.blade.php'          => 1,  // the profile page's own button
        ];

        $actual = [];

        foreach (array_keys($expected) as $relative) {
            $contents = file_get_contents(resource_path('views/frontend/'.$relative));

            $actual[$relative] = substr_count($contents, self::TRIGGER_CLASS);
        }

        $this->assertSame(
            $expected,
            $actual,
            'The number of '.self::TRIGGER_CLASS." controls per template changed.\n"
            ."If a control was added or removed, update the map in this test.\n"
            ."If it was not, a booking button has lost the class that makes it work."
        );
    }

    // ---------- helpers ----------

    /**
     * The frontend templates, minus the modal partial: its submit button
     * legitimately has no trigger class.
     *
     * @return list<string>
     */
    private function frontendTemplates(): array
    {
        $dir = resource_path('views/frontend');

        $files = iterator_to_array(new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        ));

        $paths = [];

        foreach ($files as $file) {
            $path = $file->getPathname();

            if (! str_ends_with($path, '.blade.php')) {
                continue;
            }

            if (str_ends_with($path, 'booking-modal.blade.php')) {
                continue;
            }

            $paths[] = $path;
        }

        sort($paths);

        return $paths;
    }

    /**
     * Every <button> or <a> in the document whose own text reads like a
     * booking call to action, with the class attribute it was rendered with.
     *
     * Deliberately found by TEXT, not by class: looking them up by the class
     * we are asserting on would only ever find the controls that already pass.
     *
     * @return list<array{tag: string, text: string, class: string}>
     */
    private function bookingControls(string $html): array
    {
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $found = [];

        // The modal's own submit button also reads "BOOK NOW". It closes the
        // funnel rather than opening it, so everything inside the modal is out.
        $outsideModal = '(//button | //a)[not(ancestor::*[@id="booking-modal"])]';

        foreach ((new DOMXPath($dom))->query($outsideModal) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $text = trim(preg_replace('/\s+/', ' ', $node->textContent));

            if (! preg_match('/\bbook (now|with)\b/i', $text)) {
                continue;
            }

            $found[] = [
                'tag'   => $node->tagName,
                'text'  => $text,
                'class' => $node->getAttribute('class'),
            ];
        }

        return $found;
    }
}
