<?php

namespace Tests\Feature\Portal;

use App\Http\Middleware\UseEmployeeSession;
use App\Models\Employee;
use App\Support\Portal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\RunsOnBothPortals;
use Tests\TestCase;

/**
 * The seams between the portal's two mounts.
 *
 * Everything each mount does on its own is covered twice over by the
 * parametrised tests. What is left is what only exists BECAUSE there are two:
 * one session between them, one login allowance between them, a handover from
 * the desk to the phone, and the PWA belonging to exactly one of them.
 */
class PortalBothVersionsTest extends TestCase
{
    use RefreshDatabase;
    use RunsOnBothPortals;

    /**
     * A URL as Blade's @json() writes it into the page.
     *
     * Those are not plain json_encode() flags: @json() defaults to the HEX
     * set, so a query string's "&" lands as \u0026 and a naive comparison
     * misses it.
     */
    private function asRendered(string $url): string
    {
        return json_encode($url, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    // ---------- one session ----------

    public function test_both_mounts_set_the_same_session_cookie(): void
    {
        $names = fn ($response) => collect($response->headers->getCookies())->map->getName()->all();

        $this->assertContains(UseEmployeeSession::COOKIE, $names($this->get(route('portal.login'))));

        $this->flushSession();

        $this->assertContains(UseEmployeeSession::COOKIE, $names($this->get(route('work.login'))),
            '/work did not get the portal session cookie, so it cannot share a session with /me.');
    }

    public function test_signing_in_on_the_phone_signs_you_in_at_the_desk(): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        $this->post(route('portal.login.post'), [
            'phone' => '2246698063',
            'password' => 'portal-pass-1',
        ])->assertRedirect(route('portal.today'));

        // Same session, no second sign-in.
        $this->get(route('work.today'))->assertOk();
    }

    public function test_signing_in_at_the_desk_signs_you_in_on_the_phone(): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        $this->post(route('work.login.post'), [
            'phone' => '2246698063',
            'password' => 'portal-pass-1',
        ])->assertRedirect(route('work.today'));

        $this->get(route('portal.today'))->assertOk();
    }

    public function test_logging_out_of_one_mount_logs_you_out_of_both(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $this->actingAs($barber, 'employee')->get(route('work.today'))->assertOk();

        $this->post(route('work.logout'))->assertRedirect(route('work.login'));

        $this->get(route('portal.today'))->assertRedirect(route('portal.login'));
    }

    // ---------- one login allowance ----------

    /**
     * A bare `throttle:5,1` keys on the request fingerprint, URI included, so
     * the two mounts would get five attempts each and the real limit would be
     * ten. The named limiter keys on the IP alone.
     */
    public function test_the_login_allowance_is_shared_between_the_mounts(): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('portal.login.post'), [
                'phone' => '2246698063',
                'password' => 'wrong',
            ]);
            $this->flushSession();
        }

        $this->post(route('work.login.post'), [
            'phone' => '2246698063',
            'password' => 'wrong',
        ])->assertStatus(429);
    }

    // ---------- the handover ----------

    /**
     * The redirect rule, asserted on the rule rather than on a viewport: a
     * /work page carries the /me URL for the SAME screen and the width below
     * which it hands over. Whether a browser is that narrow is the browser's
     * business.
     */
    public function test_each_desk_screen_carries_the_phone_url_for_that_same_screen(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            $body = $this->actingAs($barber, 'employee')
                ->get(route('work.'.$screen))
                ->assertOk()
                ->getContent();

            $this->assertStringContainsString($this->asRendered(route('portal.'.$screen)), $body,
                "The /work $screen page does not point at its /me counterpart.");

            $this->assertStringContainsString((string) Portal::MIN_DESKTOP_WIDTH, $body,
                "The /work $screen page does not carry the handover width.");
        }
    }

    public function test_the_handover_keeps_the_query_string(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $body = $this->actingAs($barber, 'employee')
            ->get(route('work.bookings', ['tab' => 'past', 'dir' => 'asc']))
            ->assertOk()
            ->getContent();

        // A bookmarked "past bookings, oldest first" has to survive the hop.
        $this->assertStringContainsString(
            $this->asRendered(route('portal.bookings', ['tab' => 'past', 'dir' => 'asc'])),
            $body
        );
    }

    /**
     * The handover fires on load and never again.
     *
     * A resize listener would navigate a barber away mid-form — dragging a
     * window edge, or a tiling manager rearranging things, would throw away
     * whatever they had typed into the change-password screen. The width the
     * page opened at is the one that decides.
     */
    #[DataProvider('portals')]
    public function test_no_screen_hands_over_on_resize(string $p): void
    {
        $barber = Employee::factory()->withPortal()->create();

        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            $body = $this->actingAs($barber, 'employee')
                ->get(route($p.$screen))
                ->assertOk()
                ->getContent();

            // Both the inline handover and the shared script are in play here.
            $this->assertStringNotContainsString("addEventListener('resize'", $body,
                "The $p$screen page attaches a resize listener.");
            $this->assertStringNotContainsString('addEventListener("resize"', $body,
                "The $p$screen page attaches a resize listener.");
            $this->assertStringNotContainsString('onresize', $body,
                "The $p$screen page attaches a resize handler.");
        }
    }

    /** The same, for the script both versions load from disk. */
    public function test_the_shared_script_has_no_resize_handler(): void
    {
        $source = file_get_contents(public_path('portal_assets/js/portal.js'));

        $this->assertStringNotContainsString('resize', $source,
            'portal.js reacts to resize; the handover is a load-time decision only.');
    }

    public function test_the_phone_version_has_no_handover(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $body = $this->actingAs($barber, 'employee')
            ->get(route('portal.today'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('window.innerWidth', $body,
            '/me carries a handover script; it is the version being handed to.');
    }

    public function test_the_login_screen_hands_over_too(): void
    {
        $body = $this->get(route('work.login'))->assertOk()->getContent();

        $this->assertStringContainsString($this->asRendered(route('portal.login')), $body);
    }

    public function test_the_screen_mapping_ignores_routes_outside_the_portal(): void
    {
        $this->assertSame('bookings', Portal::screenFrom('work.bookings'));
        $this->assertSame('bookings', Portal::screenFrom('portal.bookings'));
        $this->assertSame('password.edit', Portal::screenFrom('work.password.edit'));

        // Nothing outside the portal may be mapped onto a portal screen.
        $this->assertNull(Portal::screenFrom('admin.employees.index'));
        $this->assertNull(Portal::screenFrom('home'));
        $this->assertNull(Portal::screenFrom(''));
    }

    // ---------- links stay home ----------

    /**
     * A barber at the desk must not be dropped onto the phone version by a
     * link in the page they are reading — and the same the other way. Checks
     * the hrefs and form actions the page actually renders, which is where a
     * stray route() instead of the version-aware helper would show up.
     *
     * The handover script is deliberately not covered: it lives in a <script>
     * and pointing at /me is its whole job.
     */
    #[DataProvider('portals')]
    public function test_a_version_only_links_to_itself(string $p): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $here = $p === 'work.' ? '/work' : '/me';
        $there = $p === 'work.' ? '/me' : '/work';

        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            $body = $this->actingAs($barber, 'employee')
                ->get(route($p.$screen))
                ->assertOk()
                ->getContent();

            preg_match_all('/(?:href|action)="([^"]*)"/', $body, $matches);

            $strays = collect($matches[1])
                ->filter(fn ($url) => str_contains($url, $there.'/') || str_ends_with($url, $there))
                ->values()
                ->all();

            $this->assertSame([], $strays, sprintf(
                'The %s %s page links out to %s: %s',
                $here, $screen, $there, implode(', ', $strays)
            ));

            // And it really does link somewhere — a page with no links at all
            // would pass the check above for the wrong reason.
            $own = collect($matches[1])->filter(fn ($url) => str_contains($url, $here));

            $this->assertNotEmpty($own, "The $here $screen page renders no links to its own version.");
        }
    }

    // ---------- the PWA belongs to the phone ----------

    public function test_the_desk_version_serves_no_service_worker(): void
    {
        $this->get('/work/sw.js')->assertNotFound();

        // ...while the phone's is still there.
        $this->get('/me/sw.js')->assertOk();
    }

    public function test_the_desk_version_has_no_manifest_and_no_worker_registration(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            $body = $this->actingAs($barber, 'employee')
                ->get(route('work.'.$screen))
                ->assertOk()
                ->getContent();

            $this->assertStringNotContainsString('rel="manifest"', $body,
                "The /work $screen page offers a PWA manifest.");
            $this->assertStringNotContainsString('apple-touch-icon', $body,
                "The /work $screen page offers an iOS install icon.");
            $this->assertStringNotContainsString('apple-mobile-web-app-capable', $body);

            // The body class is what portal.js reads to decide whether to
            // register the worker at all.
            $this->assertStringContainsString('class="v-desktop"', $body);
        }
    }

    public function test_the_phone_version_still_offers_the_pwa(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $body = $this->actingAs($barber, 'employee')
            ->get(route('portal.today'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('rel="manifest"', $body);
        $this->assertStringContainsString('apple-touch-icon', $body);
        $this->assertStringContainsString('class="v-mobile"', $body);
    }

    /** The one script both versions load must only register a worker on one. */
    public function test_the_shared_script_registers_the_worker_only_on_the_phone(): void
    {
        $source = file_get_contents(public_path('portal_assets/js/portal.js'));

        $this->assertStringContainsString("classList.contains('v-mobile')", $source);
        $this->assertMatchesRegularExpression(
            '/isMobile\s*&&\s*.serviceWorker. in navigator/',
            $source,
            'portal.js registers a service worker without checking which version it is on.'
        );
    }

    // ---------- the mounts really are the same routes ----------

    /**
     * The guarantee the whole arrangement rests on. If a screen is ever added
     * to one mount only, this fails rather than the difference being noticed
     * months later.
     */
    public function test_both_mounts_expose_the_same_screens(): void
    {
        $screens = function (string $prefix) {
            return collect(app('router')->getRoutes()->getRoutes())
                ->map->getName()
                ->filter()
                ->filter(fn ($name) => str_starts_with($name, $prefix))
                ->map(fn ($name) => substr($name, strlen($prefix)))
                ->sort()
                ->values()
                ->all();
        };

        $phone = $screens('portal.');
        $desk = $screens('work.');

        // sw.js is the one deliberate difference.
        $this->assertSame(['sw'], array_values(array_diff($phone, $desk)),
            'The mounts differ by something other than the service worker.');

        $this->assertSame([], array_values(array_diff($desk, $phone)),
            '/work has a screen /me does not.');
    }
}
