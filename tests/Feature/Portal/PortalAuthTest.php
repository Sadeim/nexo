<?php

namespace Tests\Feature\Portal;

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Who can get into the portal, and who is kept out.
 */
class PortalAuthTest extends TestCase
{
    use RefreshDatabase;

    // ---------- signing in ----------

    public function test_a_barber_signs_in_with_phone_and_password(): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $this->post(route('portal.login.post'), [
            'phone' => '2246698063',
            'password' => 'portal-pass-1',
        ])->assertRedirect(route('portal.today'));

        $this->assertAuthenticatedAs($barber, 'employee');
    }

    /**
     * The owner types the number one way and the barber another. Both have to
     * land on the same account or the portal is unusable in practice.
     */
    public function test_punctuation_in_the_phone_number_does_not_matter(): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        $this->post(route('portal.login.post'), [
            'phone' => '(224) 669-8063',
            'password' => 'portal-pass-1',
        ])->assertRedirect(route('portal.today'));

        $this->assertAuthenticated('employee');
    }

    /**
     * The same barber, written the three ways a phone number gets written.
     * The owner saves one of them and the barber types another months later;
     * all of them have to reach the same account.
     */
    public function test_the_us_country_code_is_optional(): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        foreach (['1-224-669-8063', '+1 (224) 669-8063', '2246698063'] as $typed) {
            $this->post(route('portal.login.post'), [
                'phone' => $typed,
                'password' => 'portal-pass-1',
            ])->assertRedirect(route('portal.today'));

            $this->assertAuthenticatedAs($barber, 'employee',
                "Signing in with \"$typed\" did not reach the barber stored as 2246698063.");

            $this->post(route('portal.logout'));
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

    public function test_a_wrong_password_is_rejected(): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        $this->post(route('portal.login.post'), [
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
    public function test_every_failure_gives_the_same_message(): void
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
            $messages[] = $this->post(route('portal.login.post'), $attempt)
                ->assertSessionHasErrors('phone')
                ->getSession()->get('errors')->first('phone');

            $this->flushSession();
        }

        $this->assertCount(1, array_unique($messages),
            'The portal login gives different messages for different failures: '
            .implode(' / ', array_unique($messages)));
    }

    public function test_an_employee_with_no_password_cannot_sign_in(): void
    {
        Employee::factory()->create(['phone' => '2240000000', 'password' => null]);

        $this->post(route('portal.login.post'), [
            'phone' => '2240000000',
            'password' => '',
        ])->assertSessionHasErrors();

        $this->assertGuest('employee');
    }

    public function test_login_is_throttled(): void
    {
        Employee::factory()->withPortal('2246698063')->create();

        // The route allows five a minute; the sixth must be turned away.
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('portal.login.post'), [
                'phone' => '2246698063',
                'password' => 'wrong',
            ]);
            $this->flushSession();
        }

        $this->post(route('portal.login.post'), [
            'phone' => '2246698063',
            'password' => 'wrong',
        ])->assertStatus(429);
    }

    public function test_signing_in_records_the_time(): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $this->assertNull($barber->last_login_at);

        $this->post(route('portal.login.post'), [
            'phone' => '2246698063',
            'password' => 'portal-pass-1',
        ]);

        $this->assertNotNull($barber->fresh()->last_login_at);
    }

    public function test_logging_out_ends_the_session(): void
    {
        $barber = Employee::factory()->withPortal()->create();

        $this->actingAs($barber, 'employee')
            ->post(route('portal.logout'))
            ->assertRedirect(route('portal.login'));

        $this->assertGuest('employee');
    }

    // ---------- inactive ----------

    public function test_an_inactive_barber_is_logged_out_and_refused(): void
    {
        // Active at sign-in, switched off afterwards — the case a session-only
        // check would miss until the session expired.
        $barber = Employee::factory()->withPortal()->create();

        $this->actingAs($barber, 'employee')->get(route('portal.today'))->assertOk();

        $barber->forceFill(['is_active' => false])->save();

        $this->actingAs($barber, 'employee')
            ->get(route('portal.today'))
            ->assertForbidden();

        $this->assertGuest('employee');
    }

    // ---------- forced password change ----------

    public function test_a_barber_who_must_change_their_password_is_held_there(): void
    {
        $barber = Employee::factory()->withPortal()->mustChangePassword()->create();

        foreach (['portal.today', 'portal.bookings', 'portal.earnings', 'portal.profile'] as $route) {
            $this->actingAs($barber, 'employee')
                ->get(route($route))
                ->assertRedirect(route('portal.password.edit'));
        }

        // The one screen they are allowed to reach.
        $this->actingAs($barber, 'employee')
            ->get(route('portal.password.edit'))
            ->assertOk();
    }

    public function test_changing_the_password_releases_them(): void
    {
        $barber = Employee::factory()->withPortal()->mustChangePassword()->create();

        $this->actingAs($barber, 'employee')
            ->post(route('portal.password.update'), [
                'current_password' => 'portal-pass-1',
                'password' => 'my-own-password',
                'password_confirmation' => 'my-own-password',
            ])
            ->assertRedirect(route('portal.today'));

        $barber->refresh();

        $this->assertFalse($barber->must_change_password);
        $this->assertTrue(Hash::check('my-own-password', $barber->password));

        $this->actingAs($barber, 'employee')->get(route('portal.today'))->assertOk();
    }

    public function test_the_current_password_must_be_right_to_change_it(): void
    {
        $barber = Employee::factory()->withPortal()->mustChangePassword()->create();

        $this->actingAs($barber, 'employee')
            ->post(route('portal.password.update'), [
                'current_password' => 'not-it',
                'password' => 'my-own-password',
                'password_confirmation' => 'my-own-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue($barber->fresh()->must_change_password);
    }

    public function test_the_new_password_cannot_be_the_temporary_one(): void
    {
        $barber = Employee::factory()->withPortal()->mustChangePassword()->create();

        $this->actingAs($barber, 'employee')
            ->post(route('portal.password.update'), [
                'current_password' => 'portal-pass-1',
                'password' => 'portal-pass-1',
                'password_confirmation' => 'portal-pass-1',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue($barber->fresh()->must_change_password);
    }

    // ---------- guard isolation ----------

    public function test_a_guest_is_sent_to_the_portal_login(): void
    {
        foreach (['portal.today', 'portal.bookings', 'portal.earnings', 'portal.profile'] as $route) {
            $this->get(route($route))->assertRedirect(route('portal.login'));
        }
    }

    /**
     * The owner's admin session must not be a portal session. If it were, the
     * portal would show them an employee's screens with no employee behind it.
     */
    public function test_an_admin_session_cannot_reach_the_portal(): void
    {
        $admin = Admin::create([
            'name' => 'Owner',
            'email' => 'owner_'.uniqid().'@nexo.local',
            'password' => Hash::make('Password123'),
            'status' => 1,
        ]);

        foreach (['portal.today', 'portal.bookings', 'portal.profile'] as $route) {
            $this->actingAs($admin, 'admin')
                ->get(route($route))
                ->assertRedirect(route('portal.login'));
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
