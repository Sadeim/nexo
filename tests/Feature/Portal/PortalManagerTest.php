<?php

namespace Tests\Feature\Portal;

use App\Models\Booking;
use App\Models\Employee;
use App\Models\PosOrder;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\RunsOnBothPortals;
use Tests\TestCase;

/**
 * The manager's half of the portal.
 *
 * Eddie owns the shop and also cuts hair. So Today and Bookings are HIS —
 * exactly what a barber sees, no grouping, no chips, no other people's
 * customers — and the whole shop lives on a screen of its own, All Bookings,
 * which only a manager may open.
 *
 * Names are chosen so sort_order can be told apart from id order and from
 * alphabetical order, and so every narrowing assertion can be made on a
 * customer's name: the barbers' own names appear in the filter chips, which
 * would make assertDontSee() on a barber's name meaningless.
 */
class PortalManagerTest extends TestCase
{
    use RefreshDatabase;
    use RunsOnBothPortals;

    private Employee $boss;
    private Employee $adam;
    private Employee $zaid;
    private Service $service;
    private string $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->today = Carbon::today()->toDateString();

        $this->boss = Employee::factory()->withPortal('2240000100')->manager()
            ->create(['name' => 'Ahmed Owner', 'sort_order' => 0]);

        // Created first, sorted last: id order is not roster order.
        $this->adam = Employee::factory()->withPortal('2240000101')
            ->create(['name' => 'Adam Bakri', 'sort_order' => 9]);

        // Created second, sorted first, and alphabetically after Adam — so a
        // block order of Zaid-then-Adam can only come from sort_order.
        $this->zaid = Employee::factory()->withPortal('2240000102')
            ->create(['name' => 'Zaid Abed', 'sort_order' => 1]);

        $this->service = Service::create(['name' => 'Skin Fade', 'price' => 35]);
    }

    private function booking(?Employee $barber, string $customer, ?string $date = null, string $time = '10:00:00', string $status = 'confirmed'): Booking
    {
        return Booking::factory()->create([
            'barber_id' => $barber?->id,
            'service_id' => $this->service->id,
            'date' => $date ?? $this->today,
            'time' => $time,
            'name' => $customer,
            'status' => $status,
        ]);
    }

    /** The shop's day: one each, plus one nobody has claimed. */
    private function aNormalDay(): void
    {
        $this->booking($this->adam, 'Alpha Customer', null, '09:00:00');
        $this->booking($this->zaid, 'Bravo Customer', null, '11:00:00');
        $this->booking(null, 'Charlie Walkin', null, '13:00:00', 'pending');
    }

    // ---------- Today and Bookings belong to whoever is signed in ----------

    /**
     * The change this screen exists to prove. A manager opening Today is a
     * barber looking at their own chair.
     */
    #[DataProvider('portals')]
    public function test_a_managers_today_is_their_own_day(string $p): void
    {
        $this->aNormalDay();
        $this->booking($this->boss, 'Mine Customer', null, '15:00:00');

        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('Mine')
            ->getContent();

        foreach (['Alpha', 'Bravo', 'Charlie', 'Adam Bakri', 'Zaid Abed'] as $theirs) {
            $this->assertStringNotContainsString($theirs, $body,
                'Today showed a manager somebody else\'s work: '.$theirs);
        }

        // ...and none of the whole-shop furniture.
        $this->assertStringNotContainsString('p-chips', $body);
        $this->assertStringNotContainsString('p-group', $body);
    }

    #[DataProvider('portals')]
    public function test_a_managers_today_still_only_counts_the_unassigned(string $p): void
    {
        $this->booking(null, 'Charlie Walkin', null, '13:00:00', 'pending');

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('data-unassigned="1"', false)
            ->assertDontSee('Charlie');
    }

    #[DataProvider('portals')]
    public function test_a_managers_bookings_list_is_their_own(string $p): void
    {
        $soon = Carbon::today()->addDays(2)->toDateString();

        $this->booking($this->adam, 'Alpha Customer', $soon);
        $this->booking($this->boss, 'Mine Customer', $soon);

        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'bookings'))
            ->assertOk()
            ->assertSee('Mine')
            ->getContent();

        $this->assertStringNotContainsString('Alpha', $body);
        // No Barber column and no chips: this is one person's list.
        $this->assertStringNotContainsString('<th>Barber</th>', $body);
        $this->assertStringNotContainsString('p-chips', $body);
    }

    /** A barber's screens are untouched by any of this. */
    #[DataProvider('portals')]
    public function test_a_barbers_today_and_bookings_are_unchanged(string $p): void
    {
        $this->aNormalDay();

        $body = $this->actingAs($this->adam, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('Alpha')
            ->getContent();

        $this->assertStringNotContainsString('Bravo', $body);
        $this->assertStringNotContainsString('Charlie', $body);
        $this->assertStringNotContainsString('p-badge-manager', $body);
    }

    // ---------- All Bookings: who may open it ----------

    #[DataProvider('portals')]
    public function test_a_barber_is_refused_all_bookings(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->adam, 'employee')
            ->get(route($p.'team'))
            ->assertForbidden();

        // Including with a filter or a date on it — the gate is the route,
        // not anything in the request.
        $this->actingAs($this->adam, 'employee')
            ->get(route($p.'team', ['barber' => $this->zaid->id, 'date' => $this->today]))
            ->assertForbidden();
    }

    #[DataProvider('portals')]
    public function test_a_guest_is_sent_to_the_login_not_refused(string $p): void
    {
        $this->get(route($p.'team'))->assertRedirect(route($p.'login'));
    }

    #[DataProvider('portals')]
    public function test_a_manager_may_open_all_bookings(string $p): void
    {
        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->assertSee('All Bookings');
    }

    // ---------- All Bookings: what is on it ----------

    #[DataProvider('portals')]
    public function test_it_shows_every_barber_and_the_unassigned(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            // The blocks, by their headings.
            ->assertSee('Adam Bakri')
            ->assertSee('Zaid Abed')
            ->assertSee('Unassigned')
            // And the work inside them.
            ->assertSee('Alpha')
            ->assertSee('Bravo')
            ->assertSee('Charlie')
            ->assertSee('9:00 AM')
            ->assertSee('11:00 AM')
            ->assertSee('1:00 PM');
    }

    #[DataProvider('portals')]
    public function test_the_blocks_follow_sort_order_and_the_unassigned_come_last(string $p): void
    {
        $this->aNormalDay();

        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->getContent();

        // Zaid sorts at 1, Adam at 9 — the opposite of both id order and
        // alphabetical order, so this can only be sort_order.
        $this->assertLessThan(strpos($body, 'Adam Bakri'), strpos($body, 'Zaid Abed'));
        $this->assertGreaterThan(strpos($body, 'Adam Bakri'), strpos($body, 'Charlie'));
    }

    #[DataProvider('portals')]
    public function test_a_cancelled_unassigned_booking_is_not_waiting_work(string $p): void
    {
        $this->booking(null, 'Charlie Walkin', null, '13:00:00', 'pending');
        $this->booking(null, 'Delta GoneAway', null, '14:00:00', 'cancelled');

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->assertSee('data-unassigned="1"', false)
            ->assertSee('Charlie')
            ->assertDontSee('Delta');
    }

    #[DataProvider('portals')]
    public function test_a_deactivated_barber_keeps_their_block_for_that_days_work(string $p): void
    {
        $this->booking($this->adam, 'Alpha Customer');
        $this->adam->forceFill(['is_active' => false])->save();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->assertSee('Adam Bakri')
            ->assertSee('Alpha');
    }

    #[DataProvider('portals')]
    public function test_an_empty_day_says_so(string $p): void
    {
        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->assertSee('Nothing booked')
            ->assertSee('No appointments in the shop on');
    }

    // ---------- All Bookings: the filter ----------

    #[DataProvider('portals')]
    public function test_the_filter_narrows_to_one_barber(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team', ['barber' => $this->adam->id]))
            ->assertOk()
            ->assertSee('Alpha')
            ->assertDontSee('Bravo')
            ->assertDontSee('Charlie');
    }

    #[DataProvider('portals')]
    public function test_the_filter_narrows_to_the_unassigned(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team', ['barber' => 'unassigned']))
            ->assertOk()
            ->assertSee('Charlie')
            ->assertDontSee('Alpha')
            ->assertDontSee('Bravo');
    }

    #[DataProvider('portals')]
    public function test_filtering_to_a_quiet_barber_says_so(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team', ['barber' => $this->boss->id]))
            ->assertOk()
            ->assertSee('Ahmed Owner')
            ->assertSee('Nothing today.')
            ->assertDontSee('Alpha');
    }

    #[DataProvider('portals')]
    public function test_an_unrecognised_filter_falls_back_to_everyone(string $p): void
    {
        $this->aNormalDay();

        foreach (['999999', 'abc', '', '0', $this->adam->slug] as $nonsense) {
            $response = $this->actingAs($this->boss, 'employee')
                ->get(route($p.'team', ['barber' => $nonsense]))
                ->assertOk();

            $this->assertSame('all', $response->viewData('filter'), var_export($nonsense, true).' reached the query.');
            $response->assertSee('Alpha')->assertSee('Bravo');
        }
    }

    #[DataProvider('portals')]
    public function test_the_chips_offer_the_roster_and_the_unassigned(string $p): void
    {
        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->getContent();

        foreach ([$this->boss, $this->adam, $this->zaid] as $barber) {
            $this->assertStringContainsString('barber='.$barber->id, $body, $barber->name.' is not offered as a filter.');
        }

        $this->assertStringContainsString('barber=unassigned', $body);
    }

    // ---------- All Bookings: the day ----------

    #[DataProvider('portals')]
    public function test_it_opens_on_today(string $p): void
    {
        $this->aNormalDay();
        $this->booking($this->adam, 'Tomorrow Customer', Carbon::tomorrow()->toDateString());

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->assertSee('Alpha')
            ->assertDontSee('Tomorrow');
    }

    #[DataProvider('portals')]
    public function test_a_date_shows_that_day_and_nothing_else(string $p): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $this->aNormalDay();
        $this->booking($this->adam, 'Tomorrow Customer', $tomorrow);

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team', ['date' => $tomorrow]))
            ->assertOk()
            ->assertSee('Tomorrow')
            ->assertDontSee('Alpha')
            ->assertDontSee('Charlie');
    }

    #[DataProvider('portals')]
    public function test_the_arrows_step_one_day_at_a_time(string $p): void
    {
        $response = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team', ['date' => '2026-11-15']))
            ->assertOk();

        $this->assertSame('2026-11-14', $response->viewData('previousDay'));
        $this->assertSame('2026-11-16', $response->viewData('nextDay'));
        $this->assertFalse($response->viewData('isToday'));

        // And they are actually rendered as links to those days.
        $body = $response->getContent();
        $this->assertStringContainsString('date=2026-11-14', $body);
        $this->assertStringContainsString('date=2026-11-16', $body);
    }

    #[DataProvider('portals')]
    public function test_a_nonsense_date_falls_back_to_today(string $p): void
    {
        $this->aNormalDay();

        foreach (['', 'tomorrow', '2026-13-45', '15-11-2026', '2026-1-1', 'DROP TABLE', '0000-00-00'] as $nonsense) {
            $response = $this->actingAs($this->boss, 'employee')
                ->get(route($p.'team', ['date' => $nonsense]))
                ->assertOk();

            $this->assertSame(
                Carbon::today()->toDateString(),
                $response->viewData('day')->toDateString(),
                var_export($nonsense, true).' was accepted as a date.'
            );
        }
    }

    #[DataProvider('portals')]
    public function test_a_date_far_outside_the_shops_life_falls_back_to_today(string $p): void
    {
        foreach (['1901-01-01', '2099-12-31'] as $faraway) {
            $response = $this->actingAs($this->boss, 'employee')
                ->get(route($p.'team', ['date' => $faraway]))
                ->assertOk();

            $this->assertSame(Carbon::today()->toDateString(), $response->viewData('day')->toDateString());
        }
    }

    /** Picking a barber while looking at Thursday keeps Thursday. */
    #[DataProvider('portals')]
    public function test_the_chips_carry_the_day(string $p): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team', ['date' => $tomorrow]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('date='.$tomorrow.'&amp;barber='.$this->adam->id, $body);
    }

    /** ...and stepping a day keeps the barber. */
    #[DataProvider('portals')]
    public function test_the_arrows_carry_the_filter(string $p): void
    {
        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team', ['date' => '2026-11-15', 'barber' => $this->adam->id]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('date=2026-11-16&amp;barber='.$this->adam->id, $body);
    }

    // ---------- the navigation ----------

    #[DataProvider('portals')]
    public function test_a_manager_is_offered_the_screen_and_a_barber_is_not(string $p): void
    {
        $managerBody = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('All Bookings', $managerBody);
        $this->assertStringContainsString(route($p.'team'), $managerBody);

        $barberBody = $this->actingAs($this->adam, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('All Bookings', $barberBody);
        $this->assertStringNotContainsString(route($p.'team'), $barberBody);
    }

    #[DataProvider('portals')]
    public function test_the_header_still_badges_a_manager(string $p): void
    {
        foreach (['today', 'bookings', 'earnings', 'profile', 'team'] as $screen) {
            $this->actingAs($this->boss, 'employee')
                ->get(route($p.$screen))
                ->assertOk()
                ->assertSee('p-badge-manager', false)
                ->assertSee('Manager');
        }
    }

    // ---------- still read-only ----------

    #[DataProvider('portals')]
    public function test_all_bookings_is_read_only(string $p): void
    {
        $this->aNormalDay();

        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'team'))
            ->assertOk()
            ->getContent();

        preg_match_all('/<form[^>]*method="POST"[^>]*action="([^"]*)"/i', $body, $matches);

        $actions = array_values(array_unique(array_diff($matches[1], [route($p.'logout')])));

        $this->assertSame([], $actions, 'All Bookings posts somewhere other than logout: '.implode(', ', $actions));

        foreach (['Assign', 'Cancel booking', 'Mark complete'] as $action) {
            $this->assertStringNotContainsString($action, $body);
        }
    }

    // ---------- the scope itself ----------

    public function test_visible_to_restricts_a_barber_and_frees_a_manager(): void
    {
        $this->aNormalDay();

        $this->assertSame(1, Booking::visibleTo($this->adam)->count());
        $this->assertSame('Alpha Customer', Booking::visibleTo($this->adam)->first()->name);
        $this->assertSame(3, Booking::visibleTo($this->boss)->count());
    }

    public function test_visible_to_applies_to_pos_orders_the_same_way(): void
    {
        PosOrder::create(['order_number' => 'T-1', 'employee_id' => $this->adam->id, 'status' => 'completed']);
        PosOrder::create(['order_number' => 'T-2', 'employee_id' => $this->zaid->id, 'status' => 'completed']);

        $this->assertSame(1, PosOrder::visibleTo($this->adam)->count());
        $this->assertSame(2, PosOrder::visibleTo($this->boss)->count());
    }

    /**
     * The role is a value in a column, not a truthy flag. Anything that is
     * not exactly 'manager' must read as a barber — both for the scope and
     * for the gate in front of All Bookings.
     */
    public function test_only_the_exact_role_value_widens_what_is_visible(): void
    {
        $this->aNormalDay();

        foreach ([null, '', 'barber', 'Manager', 'managers', 'admin', '1', 'true'] as $value) {
            $this->adam->setRawAttributes(array_merge($this->adam->getAttributes(), ['portal_role' => $value]));

            $this->assertFalse($this->adam->isManager(), var_export($value, true).' was treated as the manager role.');
            $this->assertSame(1, Booking::visibleTo($this->adam)->count(), var_export($value, true).' widened what a barber sees.');
        }

        /*
         * And the gate agrees. Asserted on an in-memory value rather than a
         * stored one: the column is an enum, so the database itself refuses
         * anything but 'barber' or 'manager' — which is the first line of
         * this defence, not a reason to skip testing the second.
         */
        $impostor = Employee::factory()->withPortal('2240000103')->create();
        $impostor->setRawAttributes(array_merge($impostor->getAttributes(), ['portal_role' => 'Manager']));

        $this->actingAs($impostor, 'employee')
            ->get(route('portal.team'))
            ->assertForbidden();
    }

    public function test_an_employee_is_a_barber_unless_made_otherwise(): void
    {
        $fresh = Employee::factory()->create();

        $this->assertSame(Employee::ROLE_BARBER, $fresh->fresh()->portal_role);
        $this->assertFalse($fresh->fresh()->isManager());
    }
}
