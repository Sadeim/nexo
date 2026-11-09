<?php

namespace Tests\Feature\Portal;

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\RunsOnBothPortals;
use Tests\TestCase;

/**
 * Who can get into the portal, and who is kept out.
 */
class PortalAuthTest extends TestCase
{
    use RefreshDatabase;
    use RunsOnBothPortals;

    // ---------- signing in ----------

    #[DataProvider('portals')]
    public function test_a_barber_signs_in_with_phone_and_password(string $p): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $this->post(route($p.'login.post'), [
            'phone' => '2246698063',
            'password' => 'portal-pass-1',
        ])->assertRedirect(route($p.'today'));

        $this->assertAuthenticatedAs($barber, 'employee');
    }

    /**
     * The owner types the number one way and the barber another. Both have to
     * land on the same account or the portal is unusable in practice.
     */
    #[DataProvider('portals')]
    public function test_punctuation_in_the_phone_number_does_not_matter(string $p): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        $this->post(route($p.'login.post'), [
            'phone' => '(224) 669-8063',
            'password' => 'portal-pass-1',
        ])->assertRedirect(route($p.'today'));

        $this->assertAuthenticated('employee');
    }

    /**
     * The same barber, written the three ways a phone number gets written.
     * The owner saves one of them and the barber types another months later;
     * all of them have to reach the same account.
     */
    #[DataProvider('portals')]
    public function test_the_us_country_code_is_optional(string $p): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        foreach (['1-224-669-8063', '+1 (224) 669-8063', '2246698063'] as $typed) {
            $this->post(route($p.'login.post'), [
                'phone' => $typed,
                'password' => 'portal-pass-1',
            ])->assertRedirect(route($p.'today'));

            $this->assertAuthenticatedAs($barber, 'employee',
                "Signing in with \"$typed\" did not reach the barber stored as 2246698063.");

            $this->post(route($p.'logout'));
            $this->flushSession();
        }
    }

    /**
     * And it is dropped on the way in too, so the owner may save the number
     * in any of those forms and still be saving one number.
     */
    public function test_the_country_code_is_dropped_on_storage(): void
    {
        $this->assertSame('2246698063', Employee::normalizePhone('1-224-669-8063'));
        $this->assertSame('2246698063', Employee::normalizePhone('+1 (224) 669-8063'));
        $this->assertSame('2246698063', Employee::normalizePhone('2246698063'));
    }

    /**
     * Only eleven digits starting with 1 are a +1 prefix. Anything else keeps
     * every digit it was given.
     */
    public function test_only_an_eleven_digit_leading_one_is_treated_as_a_country_code(): void
    {
        // Ten digits that happen to start with 1 are a whole number.
        $this->assertSame('1234567890', Employee::normalizePhone('123-456-7890'));

        // Twelve digits is somebody else's country code; leave it alone.
        $this->assertSame('441224669806', Employee::normalizePhone('+44 1224 669806'));

        // Eleven digits not starting with 1 is not a +1 number.
        $this->assertSame('22466980631', Employee::normalizePhone('22466980631'));

        $this->assertNull(Employee::normalizePhone(''));
        $this->assertNull(Employee::normalizePhone('---'));
        $this->assertNull(Employee::normalizePhone(null));
    }

    #[DataProvider('portals')]
    public function test_a_wrong_password_is_rejected(string $p): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        $this->post(route($p.'login.post'), [
            'phone' => '2246698063',
            'password' => 'not-the-password',
        ])->assertSessionHasErrors('phone');

        $this->assertGuest('employee');
    }

    /**
     * Every failure says the same thing. A different message for "no such
     * number" would turn the login into a way of asking whether a given phone
     * belongs to someone who works here.
     */
    #[DataProvider('portals')]
    public function test_every_failure_gives_the_same_message(string $p): void
    {
        Employee::factory()->withPortal('2246698063')->create();
        Employee::factory()->create(['phone' => '2240000000', 'password' => null]);
        Employee::factory()->inactive()->withPortal('2241111111')->create();

        $messages = [];

        foreach ([
            ['phone' => '2246698063', 'password' => 'wrong'],      // bad password
            ['phone' => '2249999999', 'password' => 'anything'],   // no such phone
            ['phone' => '2240000000', 'password' => 'anything'],   // portal not set up
            ['phone' => '2241111111', 'password' => 'portal-pass-1'], // deactivated
        ] as $attempt) {
            $messages[] = $this->post(route($p.'login.post'), $attempt)
                ->assertSessionHasErrors('phone')
                ->getSession()->get('errors')->first('phone');

            $this->flushSession();
        }

        $this->assertCount(1, array_unique($messages),
            'The portal login gives different messages for different failures: '
            .implode(' / ', array_unique($messages)));
    }

    #[DataProvider('portals')]
    public function test_an_employee_with_no_password_cannot_sign_in(string $p): void
    {
        Employee::factory()->create(['phone' => '2240000000', 'password' => null]);

        $this->post(route($p.'login.post'), [
            'phone' => '2240000000',
            'password' => '',
        ])->assertSessionHasErrors();

        $this->assertGuest('employee');
    }

    #[DataProvider('portals')]
    public function test_login_is_throttled(string $p): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        // The route allows five a minute; the sixth must be turned away.
        for ($i = 0; $i < 5; $i++) {
            $this->post(route($p.'login.post'), [
                'phone' => '2246698063',
                'password' => 'wrong',
            ]);
            $this->flushSession();
        }

        $this->post(route($p.'login.post'), [
            'phone' => '2246698063',
            'password' => 'wrong',
        ])->assertStatus(429);
    }

    #[DataProvider('portals')]
    public function test_signing_in_records_the_time(string $p): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $this->assertNull($barber->last_login_at);

        $this->post(route($p.'login.post'), [
            'phone' => '2246698063',
            'password' => 'portal-pass-1',
        ]);

        $this->assertNotNull($barber->fresh()->last_login_at);
    }

    #[DataProvider('portals')]
    public function test_logging_out_ends_the_session(string $p): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $this->actingAs($barber, 'employee')
            ->post(route($p.'logout'))
            ->assertRedirect(route($p.'login'));

        $this->assertGuest('employee');
    }

    // ---------- inactive ----------

    #[DataProvider('portals')]
    public function test_an_inactive_barber_is_logged_out_and_refused(string $p): void
    {
        // Active at sign-in, switched off afterwards — the case a session-only
        // check would miss until the session expired.
        $barber = Employee::factory()->withPortal()->create();

        $this->actingAs($barber, 'employee')->get(route($p.'today'))->assertOk();

        $barber->forceFill(['is_active' => false])->save();

        $this->actingAs($barber, 'employee')
            ->get(route($p.'today'))
            ->assertForbidden();

        $this->assertGuest('employee');
    }

    // ---------- changing a password ----------

    /**
     * Nothing stands between a barber and their day.
     *
     * The portal used to hold anyone whose password had been typed by the
     * shop on a "Choose your password" screen until they changed it. On
     * 9 Nov 2026 a barber signed in at 13:15 and was still stuck on it hours
     * later — the screen's own pull-to-refresh gesture emptied the form every
     * time he reached for the Save button. The gate is gone; the flag it read
     * is still written, and still shown to the owner in the dashboard.
     */
    #[DataProvider('portals')]
    public function test_a_barber_whose_password_was_set_for_them_still_lands_on_today(string $p): void
    {
        $barber = Employee::factory()->withPortal('2240000031')->mustChangePassword()->create();

        $this->post(route($p.'login.post'), [
            'phone' => '2240000031',
            'password' => 'portal-pass-1',
        ])->assertRedirect(route($p.'today'));

        // And every screen is reachable, not just the one.
        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            $this->actingAs($barber, 'employee')
                ->get(route($p.$screen))
                ->assertOk();
        }

        // The flag is untouched by any of that: it is a record of who chose
        // the password, which the dashboard reports.
        $this->assertTrue($barber->fresh()->must_change_password);
    }

    /** The form is on Profile, and it is the only place it is offered. */
    #[DataProvider('portals')]
    public function test_the_change_password_form_lives_on_profile(string $p): void
    {
        $barber = Employee::factory()->withPortal('2240000032')->create();

        $body = $this->actingAs($barber, 'employee')
            ->get(route($p.'profile'))
            ->assertOk()
            ->assertSee('Change password')
            ->getContent();

        $this->assertStringContainsString('action="'.route($p.'password.update').'"', $body);

        foreach (['current_password', 'password', 'password_confirmation'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $body);
        }

        // No screen of its own left to link to. The form POSTs to
        // .../password, so this looks at links, not at every mention of it.
        preg_match_all('/href="([^"]*)"/', $body, $links);

        $this->assertSame(
            [],
            array_values(array_filter($links[1], fn ($href) => str_contains($href, 'password'))),
            'Profile still links out to a password screen.'
        );
    }

    public function test_there_is_no_separate_password_screen_on_either_mount(): void
    {
        foreach (['portal.', 'work.'] as $p) {
            $this->assertFalse(
                \Illuminate\Support\Facades\Route::has($p.'password.edit'),
                $p.'password.edit still exists.'
            );
        }

        $this->get('/me/password')->assertStatus(405);
        $this->get('/work/password')->assertStatus(405);
    }

    #[DataProvider('portals')]
    public function test_changing_the_password_works_and_clears_the_flag(string $p): void
    {
        $barber = Employee::factory()->withPortal()->mustChangePassword()->create();

        $this->actingAs($barber, 'employee')
            ->post(route($p.'password.update'), [
                'current_password' => 'portal-pass-1',
                'password' => 'my-own-password',
                'password_confirmation' => 'my-own-password',
            ])
            // Back to the form's own screen, not to Today.
            ->assertRedirect(route($p.'profile'));

        $barber->refresh();

        $this->assertFalse($barber->must_change_password);
        $this->assertTrue(Hash::check('my-own-password', $barber->password));

        $this->actingAs($barber, 'employee')->get(route($p.'today'))->assertOk();
    }

    #[DataProvider('portals')]
    public function test_the_current_password_must_be_right_to_change_it(string $p): void
    {
        $barber = Employee::factory()->withPortal()->mustChangePassword()->create();

        $this->actingAs($barber, 'employee')
            ->post(route($p.'password.update'), [
                'current_password' => 'not-it',
                'password' => 'my-own-password',
                'password_confirmation' => 'my-own-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue($barber->fresh()->must_change_password);
    }

    #[DataProvider('portals')]
    public function test_the_new_password_cannot_be_the_one_they_already_have(string $p): void
    {
        $barber = Employee::factory()->withPortal()->mustChangePassword()->create();

        $this->actingAs($barber, 'employee')
            ->post(route($p.'password.update'), [
                'current_password' => 'portal-pass-1',
                'password' => 'portal-pass-1',
                'password_confirmation' => 'portal-pass-1',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue($barber->fresh()->must_change_password);
    }

    /** A rejected change sends them back to the screen the form is on. */
    #[DataProvider('portals')]
    public function test_a_rejected_change_returns_to_profile(string $p): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $this->actingAs($barber, 'employee')
            ->from(route($p.'profile'))
            ->post(route($p.'password.update'), [
                'current_password' => 'not-it',
                'password' => 'my-own-password',
                'password_confirmation' => 'my-own-password',
            ])
            ->assertRedirect(route($p.'profile'));

        // And the error is rendered where it lands.
        $this->actingAs($barber, 'employee')
            ->get(route($p.'profile'))
            ->assertOk()
            ->assertSee('That is not your current password.');
    }

    // ---------- guard isolation ----------

    #[DataProvider('portals')]
    public function test_a_guest_is_sent_to_the_portal_login(string $p): void
    {
        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            $this->get(route($p.$screen))->assertRedirect(route($p.'login'));
        }
    }

    /**
     * The owner's admin session must not be a portal session. If it were, the
     * portal would show them an employee's screens with no employee behind it.
     */
    #[DataProvider('portals')]
    public function test_an_admin_session_cannot_reach_the_portal(string $p): void
    {
        $admin = Admin::create([
            'name' => 'Owner',
            'email' => 'owner_'.uniqid().'@nexo.local',
            'password' => Hash::make('Password123'),
            'status' => 1,
        ]);

        foreach (['today', 'bookings', 'profile'] as $screen) {
            $this->actingAs($admin, 'admin')
                ->get(route($p.$screen))
                ->assertRedirect(route($p.'login'));
        }
    }

    public function test_an_employee_session_cannot_reach_the_admin_area(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $response = $this->actingAs($barber, 'employee')->get('/admin/employees');

        $this->assertNotEquals(200, $response->getStatusCode(),
            'An employee session reached an admin screen.');
    }

    public function test_an_employee_session_cannot_reach_the_pos(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $response = $this->actingAs($barber, 'employee')->get('/pos');

        $this->assertNotEquals(200, $response->getStatusCode(),
            'An employee session reached the POS.');
    }
}
