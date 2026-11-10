<?php

namespace Tests\Feature\Marketing;

use App\Models\Employee;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Google Ads tag belongs on the public website and nowhere else.
 *
 * The dashboard is the shop's back office and the portal is a barber's
 * private schedule; neither is a page a marketing tag has any business
 * loading a third-party script onto.
 */
class GoogleAdsTagTest extends TestCase
{
    use RefreshDatabase;

    private const ID = 'AW-18136253376';

    protected function setUp(): void
    {
        parent::setUp();

        // The public pages render CMS content, so they need the fixture set —
        // the same reason BookingEntryPointsTest seeds.
        $this->seed(DatabaseSeeder::class);
    }

    private function withTag(): void
    {
        config(['services.google_ads.id' => self::ID]);
    }

    // ---------- where it goes ----------

    public function test_the_public_site_carries_the_tag(): void
    {
        $this->withTag();

        $body = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('googletagmanager.com/gtag/js?id='.self::ID, $body);
        $this->assertStringContainsString("gtag('config', '".self::ID."')", $body);
        $this->assertStringContainsString('<!-- Google tag (gtag.js) -->', $body);
    }

    public function test_other_public_pages_carry_it_too(): void
    {
        $this->withTag();

        foreach (['/service', '/contact_us'] as $page) {
            $this->assertStringContainsString(
                self::ID,
                $this->get($page)->assertOk()->getContent(),
                $page.' is a public page and has no tag.'
            );
        }
    }

    public function test_the_dashboard_login_does_not_carry_it(): void
    {
        $this->withTag();

        $this->get('/admin/login')
            ->assertOk()
            ->assertDontSee(self::ID)
            ->assertDontSee('googletagmanager.com', false);
    }

    public function test_the_barber_portal_does_not_carry_it(): void
    {
        $this->withTag();

        foreach (['/me/login', '/work/login'] as $page) {
            $this->get($page)
                ->assertOk()
                ->assertDontSee(self::ID)
                ->assertDontSee('googletagmanager.com', false);
        }
    }

    public function test_a_signed_in_barbers_screens_do_not_carry_it(): void
    {
        $this->withTag();

        $barber = Employee::factory()->withPortal('2240000061')->create();

        foreach (['portal.today', 'portal.profile', 'work.today', 'work.bookings'] as $screen) {
            $this->actingAs($barber, 'employee')
                ->get(route($screen))
                ->assertOk()
                ->assertDontSee(self::ID);
        }
    }

    // ---------- when it is absent ----------

    /**
     * No id, no tag. Local and staging carry no GOOGLE_ADS_ID and must not
     * report a single thing to a live Ads account.
     */
    public function test_nothing_is_rendered_without_an_id(): void
    {
        config(['services.google_ads.id' => null]);

        $body = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('googletagmanager.com', $body);
        $this->assertStringNotContainsString('gtag(', $body);
    }

    // ---------- the conversion ----------

    public function test_the_modal_carries_the_conversion_target(): void
    {
        config([
            'services.google_ads.id' => self::ID,
            'services.google_ads.booking_label' => 'AbC-D_efGh',
        ]);

        $body = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-ads-id="'.self::ID.'"', $body);
        $this->assertStringContainsString('data-ads-label="AbC-D_efGh"', $body);
    }

    /** The label is not configured yet, and that must be harmless. */
    public function test_an_empty_label_renders_an_empty_attribute(): void
    {
        config([
            'services.google_ads.id' => self::ID,
            'services.google_ads.booking_label' => null,
        ]);

        $this->get('/')->assertOk()->assertSee('data-ads-label=""', false);
    }

    /**
     * The script reports only when it has somewhere to report to, and can
     * never take a confirmed booking down with it.
     */
    public function test_the_script_guards_every_way_the_tag_can_be_missing(): void
    {
        $source = file_get_contents(public_path('frontend_assets/js/booking.js'));

        $this->assertStringContainsString("typeof window.gtag !== 'function'", $source,
            'The conversion does not check that gtag loaded.');
        $this->assertStringContainsString('if (!adsId || !label', $source,
            'The conversion fires without an id or a label.');
        $this->assertMatchesRegularExpression('/function reportConversion\(\)\s*\{\s*try\s*\{/', $source,
            'The conversion is not wrapped in a try block.');

        // It is actually called on success...
        $this->assertStringContainsString('reportConversion();', $source,
            'Nothing ever reports a conversion.');

        // ...and before the form is cleared, or the price is already gone.
        // Asserted only once the call is known to exist: strpos returns false
        // for a missing needle, and false compares as less than anything.
        $this->assertLessThan(
            strpos($source, 'form.reset();'),
            strpos($source, 'reportConversion();'),
            'The conversion is reported after the form has been reset.'
        );
    }

    public function test_the_services_endpoint_supplies_a_price_for_the_value(): void
    {
        $service = Service::create(['name' => 'Conversion Value Fade', 'price' => 35]);

        $services = $this->getJson(route('services.get'))->assertOk()->json('services');

        $mine = collect($services)->firstWhere('id', $service->id);

        $this->assertNotNull($mine, 'The services endpoint did not return the service.');
        $this->assertArrayHasKey('price', $mine, 'The conversion has no value to report.');
        $this->assertEquals(35, $mine['price']);
    }
}
