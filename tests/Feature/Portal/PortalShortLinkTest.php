<?php

namespace Tests\Feature\Portal;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * /app — the address the shop says out loud.
 *
 * The whole feature is a redirect, and the whole risk is that it stops being
 * one. A portal served at two paths would have two scopes, two sets of
 * absolute links and two places for a service worker to register, and the
 * installed app on every barber's phone is pinned to /me.
 */
class PortalShortLinkTest extends TestCase
{
    use RefreshDatabase;

    // ---------- it goes where it says ----------

    public function test_the_short_link_lands_on_the_portal(): void
    {
        $this->get('/app')
            ->assertStatus(302)
            ->assertRedirect('/me');
    }

    public function test_a_path_is_carried_across(): void
    {
        foreach (['bookings', 'profile', 'earnings', 'login', 'team'] as $screen) {
            $this->get('/app/'.$screen)
                ->assertStatus(302)
                ->assertRedirect('/me/'.$screen);
        }
    }

    /**
     * A link to "past bookings, newest first" has to arrive as that screen,
     * not as the portal's front door. The desk-to-phone handover carries
     * query strings for the same reason.
     *
     * Compared as parameters rather than as a string: the query string is
     * normalised on the way through, so the pairs are what survives, not
     * the order they were typed in.
     */
    public function test_the_query_string_survives(): void
    {
        $cases = [
            '/app/bookings?tab=past&dir=desc' => ['/me/bookings', ['tab' => 'past', 'dir' => 'desc']],
            '/app/team?date=2026-11-11&barber=3' => ['/me/team', ['date' => '2026-11-11', 'barber' => '3']],
            '/app?utm_source=card' => ['/me', ['utm_source' => 'card']],
        ];

        foreach ($cases as $from => [$path, $expected]) {
            $location = $this->get($from)->assertStatus(302)->headers->get('Location');

            $this->assertSame(url($path), strtok($location, '?'), "$from landed on the wrong screen.");

            parse_str((string) parse_url($location, PHP_URL_QUERY), $got);

            // assertEquals, not assertSame: the pairs are what matters, and
            // normalisation is free to reorder them.
            $this->assertEquals($expected, $got, "$from lost or changed a parameter.");
        }
    }

    public function test_the_dotted_shell_files_are_reachable_through_it(): void
    {
        $this->get('/app/sw.js')->assertRedirect('/me/sw.js');
        $this->get('/app/manifest.webmanifest')->assertRedirect('/me/manifest.webmanifest');
    }

    /** Following it all the way through has to end on a real screen. */
    public function test_following_it_signed_in_reaches_today(): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $this->actingAs($barber, 'employee')
            ->get('/app')
            ->assertRedirect('/me');

        $this->actingAs($barber, 'employee')
            ->get('/me')
            ->assertOk();
    }

    public function test_following_it_signed_out_reaches_the_login(): void
    {
        $this->get('/app/bookings')->assertRedirect('/me/bookings');

        $this->get('/me/bookings')->assertRedirect(route('portal.login'));
    }

    // ---------- and nothing else ----------

    /**
     * The one rule. Every route under /app must be the redirect — a second
     * mount of the portal here would be a second scope for the service
     * worker and a second set of session-cookie paths, and the installed app
     * on every barber's phone is registered against /me.
     */
    public function test_nothing_is_served_under_app(): void
    {
        $mounted = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'app/') || $route->uri() === 'app')
            ->map(fn ($route) => $route->uri().' -> '.$route->getActionName())
            ->values()
            ->all();

        $this->assertCount(1, $mounted,
            "More than the redirect is mounted under /app:\n".implode("\n", $mounted));

        $this->assertStringContainsString('ShortLinkController', $mounted[0]);
    }

    public function test_the_short_link_only_answers_get(): void
    {
        // Not a login endpoint, not a second place to post a password to.
        $this->post('/app/login', ['phone' => '2246698063', 'password' => 'x'])
            ->assertStatus(405);
    }

    /**
     * The real app stays at /me. If this ever fails, every installed phone
     * has been orphaned: the manifest's scope, the worker's registration and
     * the home-screen shortcut all point at /me.
     */
    public function test_the_portal_still_lives_at_me(): void
    {
        $this->get('/me/login')->assertOk();

        $manifest = $this->get(route('portal.manifest'))->assertOk()->json();

        $this->assertSame('/me', $manifest['start_url']);
        $this->assertSame('/me', $manifest['scope']);
    }

    /**
     * A path that is not a portal URL is a 404, not a redirect assembled out
     * of whatever was in it.
     */
    public function test_a_path_that_is_not_portal_shaped_is_refused(): void
    {
        foreach (['/app/%20spaces', '/app/sql%27inject', '/app/a%0d%0aX:1'] as $nonsense) {
            $response = $this->get($nonsense);

            $this->assertNotEquals(302, $response->getStatusCode(),
                "$nonsense was turned into a redirect.");
        }
    }

    /** Whatever comes out, it is a path on this site and nowhere else. */
    public function test_it_cannot_be_pointed_off_site(): void
    {
        foreach (['/app//evil.example.com', '/app/../../etc/passwd', '/app/https:/evil.example.com'] as $attempt) {
            $response = $this->get($attempt);

            if ($response->getStatusCode() !== 302) {
                continue;
            }

            $this->assertStringStartsWith(url('/me'), $response->headers->get('Location'),
                "$attempt left the site.");
        }
    }
}
