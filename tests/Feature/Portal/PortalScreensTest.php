<?php

namespace Tests\Feature\Portal;

use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\RunsOnBothPortals;
use Tests\TestCase;

/**
 * The four portal screens: that they render, and that each one shows the
 * signed-in barber's data and nobody else's.
 */
class PortalScreensTest extends TestCase
{
    use RefreshDatabase;
    use RunsOnBothPortals;

    private Employee $me;
    private Employee $other;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->me = Employee::factory()->withPortal('2240000001')->create(['name' => 'Omar Haddad']);
        $this->other = Employee::factory()->withPortal('2240000002')->create(['name' => 'Eddie Nasser']);

        $this->service = Service::create([
            'name' => 'Skin Fade',
            'price' => 35,
        ]);
    }

    private function bookingFor(Employee $barber, string $date, string $time, string $customer, string $status = 'confirmed'): Booking
    {
        return Booking::factory()->create([
            'barber_id' => $barber->id,
            'service_id' => $this->service->id,
            'date' => $date,
            'time' => $time,
            'name' => $customer,
            'status' => $status,
        ]);
    }

    // ---------- Today ----------

    #[DataProvider('portals')]
    public function test_today_shows_my_bookings_for_today(string $p): void
    {
        $today = Carbon::today()->toDateString();

        $this->bookingFor($this->me, $today, '09:00:00', 'Tariq Customer');
        $this->bookingFor($this->me, $today, '14:20:00', 'Sami Customer');

        $this->actingAs($this->me, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('Tariq')
            ->assertSee('Sami')
            ->assertSee('9:00 AM')
            ->assertSee('2:20 PM')
            ->assertSee('Skin Fade');
    }

    #[DataProvider('portals')]
    public function test_today_has_an_empty_state(string $p): void
    {
        $this->actingAs($this->me, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('Nothing booked');
    }

    #[DataProvider('portals')]
    public function test_today_excludes_other_days_and_other_barbers(string $p): void
    {
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        $this->bookingFor($this->me, $today, '10:00:00', 'Mine Today');
        $this->bookingFor($this->me, $tomorrow, '10:00:00', 'MineTomorrow');
        $this->bookingFor($this->other, $today, '11:00:00', 'Theirs Today');

        $this->actingAs($this->me, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('Mine')
            ->assertDontSee('MineTomorrow')
            ->assertDontSee('Theirs');
    }

    /**
     * The unassigned line is a count and only a count: a barber should know
     * there is unclaimed work without being handed other people's customers.
     */
    #[DataProvider('portals')]
    public function test_today_counts_unassigned_bookings_without_naming_them(string $p): void
    {
        $today = Carbon::today()->toDateString();

        Booking::factory()->create([
            'barber_id' => null, 'date' => $today, 'time' => '12:00:00',
            'name' => 'Unclaimed Person', 'status' => 'pending',
        ]);
        Booking::factory()->create([
            'barber_id' => null, 'date' => $today, 'time' => '13:00:00',
            'name' => 'Another Unclaimed', 'status' => 'confirmed',
        ]);
        // Cancelled ones are not work waiting to be done.
        Booking::factory()->cancelled()->create([
            'barber_id' => null, 'date' => $today, 'time' => '15:00:00', 'name' => 'Gone Away',
        ]);

        $this->actingAs($this->me, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('data-unassigned="2"', false)
            ->assertSee('Unassigned today')
            ->assertDontSee('Unclaimed')
            ->assertDontSee('Gone Away');
    }

    // ---------- Bookings ----------

    #[DataProvider('portals')]
    public function test_bookings_upcoming_and_past_split_on_today(string $p): void
    {
        $this->bookingFor($this->me, Carbon::today()->addDays(3)->toDateString(), '10:00:00', 'Future Customer');
        $this->bookingFor($this->me, Carbon::today()->subDays(3)->toDateString(), '10:00:00', 'Previous Customer');

        $this->actingAs($this->me, 'employee')
            ->get(route($p.'bookings'))
            ->assertOk()
            ->assertSee('Future')
            ->assertDontSee('Previous');

        $this->actingAs($this->me, 'employee')
            ->get(route($p.'bookings', ['tab' => 'past']))
            ->assertOk()
            ->assertSee('Previous')
            ->assertDontSee('Future');
    }

    #[DataProvider('portals')]
    public function test_bookings_has_empty_states_on_both_tabs(string $p): void
    {
        $this->actingAs($this->me, 'employee')
            ->get(route($p.'bookings'))
            ->assertOk()
            ->assertSee('Nothing coming up');

        $this->actingAs($this->me, 'employee')
            ->get(route($p.'bookings', ['tab' => 'past']))
            ->assertOk()
            ->assertSee('No history yet');
    }

    #[DataProvider('portals')]
    public function test_bookings_paginates(string $p): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->bookingFor(
                $this->me,
                Carbon::today()->addDays($i)->toDateString(),
                '10:00:00',
                'Customer'.$i
            );
        }

        $first = $this->actingAs($this->me, 'employee')->get(route($p.'bookings'));
        $first->assertOk()->assertSee('Older');

        // Page two exists and carries the rest.
        $this->actingAs($this->me, 'employee')
            ->get(route($p.'bookings', ['page' => 2]))
            ->assertOk()
            ->assertSee('Newer');
    }

    #[DataProvider('portals')]
    public function test_the_status_is_shown_as_a_badge(string $p): void
    {
        $this->bookingFor($this->me, Carbon::today()->toDateString(), '10:00:00', 'Someone', 'confirmed');

        $this->actingAs($this->me, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('p-badge-confirmed', false);
    }

    // ---------- Earnings ----------

    #[DataProvider('portals')]
    public function test_earnings_is_a_placeholder_and_computes_nothing(string $p): void
    {
        $this->actingAs($this->me, 'employee')
            ->get(route($p.'earnings'))
            ->assertOk()
            ->assertSee('Coming soon')
            // No money anywhere on the screen.
            ->assertDontSee('$');
    }

    // ---------- Profile ----------

    #[DataProvider('portals')]
    public function test_profile_shows_my_public_details_read_only(string $p): void
    {
        $this->me->forceFill([
            'title' => 'Master Barber',
            'bio' => 'Twelve years behind the chair.',
            'public_profile' => true,
        ])->save();

        $response = $this->actingAs($this->me, 'employee')
            ->get(route($p.'profile'))
            ->assertOk()
            ->assertSee('Omar Haddad')
            ->assertSee('Master Barber')
            ->assertSee('Twelve years behind the chair.')
            ->assertSee('Change password')
            ->assertSee('Log out');

        // Read-only: nothing on this screen posts a profile change.
        $this->assertStringNotContainsString('name="bio"', $response->getContent());
        $this->assertStringNotContainsString('name="title"', $response->getContent());
    }

    #[DataProvider('portals')]
    public function test_profile_renders_for_a_barber_with_nothing_filled_in(string $p): void
    {
        $bare = Employee::factory()->withPortal('2240000009')->create([
            'name' => 'New Starter', 'title' => null, 'bio' => null, 'photo_path' => null,
        ]);

        $this->actingAs($bare, 'employee')
            ->get(route($p.'profile'))
            ->assertOk()
            ->assertSee('New Starter')
            ->assertSee('Barber');
    }

    #[DataProvider('portals')]
    public function test_profile_never_renders_the_password_hash(string $p): void
    {
        $response = $this->actingAs($this->me, 'employee')->get(route($p.'profile'));

        $this->assertStringNotContainsString(
            $this->me->fresh()->password,
            $response->getContent(),
            'The password hash was rendered into the profile page.'
        );
    }

    // ---------- scope leakage ----------

    /**
     * No portal route takes an id, so the attack is to supply one anyway and
     * hope something reads it. Every known shape is tried against every
     * screen; none of them may surface the other barber's customer.
     */
    #[DataProvider('portals')]
    public function test_no_query_parameter_can_make_the_portal_show_another_barber(string $p): void
    {
        $today = Carbon::today()->toDateString();

        $this->bookingFor($this->me, $today, '09:00:00', 'Mine Customer');
        $theirs = $this->bookingFor($this->other, $today, '10:00:00', 'Theirs Customer');
        $this->bookingFor($this->other, Carbon::today()->subDay()->toDateString(), '10:00:00', 'TheirsPast');

        $tampered = [
            ['employee_id' => $this->other->id],
            ['barber_id' => $this->other->id],
            ['id' => $this->other->id],
            ['employee' => $this->other->slug],
            ['slug' => $this->other->slug],
            ['booking_id' => $theirs->id],
            ['user_id' => $this->other->id],
            ['tab' => 'past', 'barber_id' => $this->other->id],
        ];

        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            foreach ($tampered as $query) {
                $response = $this->actingAs($this->me, 'employee')
                    ->get(route($p.$screen, $query));

                $response->assertOk();

                $body = $response->getContent();

                $this->assertStringNotContainsString('Theirs', $body, sprintf(
                    '%s leaked the other barber with %s', $p.$screen, json_encode($query)
                ));
                $this->assertStringNotContainsString('Eddie Nasser', $body, sprintf(
                    '%s leaked the other barber\'s name with %s', $p.$screen, json_encode($query)
                ));
            }
        }
    }

    /**
     * The guard against the scope being called with nothing. A no-op scope
     * would turn a bug into "every barber's bookings", which is the one
     * failure that must be loud.
     */
    public function test_the_scope_refuses_an_empty_employee_id(): void
    {
        foreach ([null, 0, '', 'abc'] as $bad) {
            try {
                Booking::forEmployee($bad)->count();
                $this->fail('forEmployee() accepted '.var_export($bad, true));
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('employee id', $e->getMessage());
            }
        }
    }

    // ---------- the service worker ----------

    public function test_the_service_worker_is_served_in_scope_and_without_login(): void
    {
        $response = $this->get('/me/sw.js');

        $response->assertOk();
        $response->assertHeader('Service-Worker-Allowed', '/me/');
        $this->assertStringContainsString('javascript', $response->headers->get('Content-Type'));
    }

    /**
     * The one rule the worker exists under. A cached /me response is one
     * barber's schedule waiting to be shown to the next person holding the
     * phone.
     */
    public function test_the_service_worker_never_caches_portal_responses(): void
    {
        $source = file_get_contents(public_path('portal_assets/sw.js'));

        $this->assertStringContainsString("startsWith('/portal_assets/')", $source,
            'The worker no longer limits its cache to the shell directory.');

        foreach (["'/me'", '"/me"', "'/me/'", 'me/bookings', 'me/today'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden.',', $source,
                'A /me path appears in the service worker cache list.');
        }
    }
}
