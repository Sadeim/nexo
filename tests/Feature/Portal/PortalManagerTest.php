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
 * The manager's half of the portal: the owner reading the whole shop.
 *
 * Two things are being tested at once, and the second matters more. One is
 * that a manager sees everyone — blocks per barber, the unassigned bookings by
 * name, a filter that narrows. The other is that NOTHING here reaches a
 * barber: the role lives in the employee's own row, the ?barber= parameter is
 * dropped before it is read for anyone who is not a manager, and the barber
 * tests in PortalScreensTest still bite unchanged.
 *
 * Names are chosen so that sort_order can be told apart from id order and
 * from alphabetical order, and so that every narrowing assertion can be made
 * on a customer's name — the barbers' own names appear in the filter chips,
 * which would make assertDontSee() on a barber's name meaningless.
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

    /** The shop's day: one booking each, plus one nobody has claimed. */
    private function aNormalDay(): void
    {
        $this->booking($this->adam, 'Alpha Customer', null, '09:00:00');
        $this->booking($this->zaid, 'Bravo Customer', null, '11:00:00');
        $this->booking(null, 'Charlie Walkin', null, '13:00:00', 'pending');
    }

    // ---------- Today ----------

    #[DataProvider('portals')]
    public function test_a_manager_sees_every_barbers_bookings_today(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
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

    /**
     * The difference from a barber's screen. A barber is told a number; the
     * manager is the person who decides who takes the work, so they get the
     * bookings themselves.
     */
    #[DataProvider('portals')]
    public function test_the_unassigned_are_listed_for_a_manager_and_counted_for_a_barber(string $p): void
    {
        $this->booking(null, 'Charlie Walkin', null, '13:00:00', 'pending');

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('data-unassigned="1"', false)
            ->assertSee('Charlie');

        // The same day, read by a barber: the count, and no names.
        $this->actingAs($this->adam, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('data-unassigned="1"', false)
            ->assertDontSee('Charlie');
    }

    #[DataProvider('portals')]
    public function test_the_blocks_follow_sort_order(string $p): void
    {
        $this->aNormalDay();

        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->getContent();

        // Zaid sorts at 1, Adam at 9 — the opposite of both id order and
        // alphabetical order, so this can only be sort_order.
        $this->assertLessThan(
            strpos($body, 'Adam Bakri'),
            strpos($body, 'Zaid Abed'),
            'The blocks are not in roster order.'
        );

        // And the unclaimed work comes after the people, not among them.
        $this->assertGreaterThan(
            strpos($body, 'Adam Bakri'),
            strpos($body, 'Charlie'),
            'The unassigned block is not last.'
        );
    }

    #[DataProvider('portals')]
    public function test_a_cancelled_unassigned_booking_is_not_waiting_work(string $p): void
    {
        $this->booking(null, 'Charlie Walkin', null, '13:00:00', 'pending');
        Booking::factory()->cancelled()->create([
            'barber_id' => null, 'service_id' => $this->service->id,
            'date' => $this->today, 'time' => '14:00:00', 'name' => 'Delta GoneAway',
        ]);

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('data-unassigned="1"', false)
            ->assertSee('Charlie')
            ->assertDontSee('Delta');
    }

    #[DataProvider('portals')]
    public function test_a_manager_with_an_empty_shop_day_gets_an_empty_state(string $p): void
    {
        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('Nothing booked')
            ->assertSee('No appointments in the shop today.');
    }

    /**
     * A barber who stops working mid-morning still has people in the diary.
     * Their block has to survive being taken off the active roster, or the one
     * screen whose job is the whole day quietly loses part of it.
     */
    #[DataProvider('portals')]
    public function test_a_deactivated_barber_keeps_their_block_for_todays_work(string $p): void
    {
        $this->booking($this->adam, 'Alpha Customer');
        $this->adam->forceFill(['is_active' => false])->save();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->assertSee('Adam Bakri')
            ->assertSee('Alpha');
    }

    // ---------- the filter ----------

    #[DataProvider('portals')]
    public function test_the_filter_narrows_to_one_barber(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today', ['barber' => $this->adam->id]))
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
            ->get(route($p.'today', ['barber' => 'unassigned']))
            ->assertOk()
            ->assertSee('Charlie')
            ->assertDontSee('Alpha')
            ->assertDontSee('Bravo');
    }

    #[DataProvider('portals')]
    public function test_filtering_to_a_quiet_barber_says_so_rather_than_showing_nothing(string $p): void
    {
        $this->aNormalDay();

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today', ['barber' => $this->boss->id]))
            ->assertOk()
            ->assertSee('Ahmed Owner')
            ->assertSee('Nothing today.')
            ->assertDontSee('Alpha');
    }

    /**
     * An id that is not on the roster must not reach a query. Falling back to
     * All is the same shape as the tab and sort parameters already use.
     */
    #[DataProvider('portals')]
    public function test_an_unrecognised_filter_falls_back_to_everyone(string $p): void
    {
        $this->aNormalDay();

        foreach (['999999', 'abc', '', '0', 'null', $this->adam->slug] as $nonsense) {
            $this->actingAs($this->boss, 'employee')
                ->get(route($p.'today', ['barber' => $nonsense]))
                ->assertOk()
                ->assertSee('Alpha')
                ->assertSee('Bravo');
        }
    }

    #[DataProvider('portals')]
    public function test_the_chips_offer_the_roster_and_the_unassigned(string $p): void
    {
        $body = $this->actingAs($this->boss, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->getContent();

        foreach ([$this->boss, $this->adam, $this->zaid] as $barber) {
            $this->assertStringContainsString(
                'barber='.$barber->id,
                $body,
                $barber->name.' is not offered as a filter.'
            );
        }

        $this->assertStringContainsString('barber=unassigned', $body);
    }

    // ---------- Bookings ----------

    #[DataProvider('portals')]
    public function test_a_manager_sees_every_barbers_bookings_in_the_list(string $p): void
    {
        $soon = Carbon::today()->addDays(2)->toDateString();

        $this->booking($this->adam, 'Alpha Customer', $soon);
        $this->booking($this->zaid, 'Bravo Customer', $soon);
        $this->booking(null, 'Charlie Walkin', $soon, '12:00:00', 'pending');

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'bookings'))
            ->assertOk()
            ->assertSee('Alpha')
            ->assertSee('Bravo')
            ->assertSee('Charlie')
            // Whose each one is, which a barber's own list has no column for.
            ->assertSee('Barber')
            ->assertSee('Adam Bakri')
            ->assertSee('Zaid Abed');
    }

    #[DataProvider('portals')]
    public function test_the_bookings_filter_narrows_and_keeps_the_tab(string $p): void
    {
        $past = Carbon::today()->subDays(2)->toDateString();

        $this->booking($this->adam, 'Alpha Customer', $past);
        $this->booking($this->zaid, 'Bravo Customer', $past);

        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'bookings', ['tab' => 'past', 'barber' => $this->adam->id]))
            ->assertOk()
            ->assertSee('Alpha')
            ->assertDontSee('Bravo');

        // The filter did not quietly move them back to Upcoming.
        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'bookings', ['tab' => 'past', 'barber' => $this->zaid->id]))
            ->assertOk()
            ->assertSee('Bravo')
            ->assertDontSee('Alpha');
    }

    #[DataProvider('portals')]
    public function test_a_manager_with_nothing_matching_gets_an_empty_state(string $p): void
    {
        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'bookings'))
            ->assertOk()
            ->assertSee('Nothing coming up');
    }

    // ---------- a barber is unaffected ----------

    /**
     * The whole point. ?barber= is a manager's control; in a barber's hands it
     * must do nothing at all — not widen the view, not narrow it onto someone
     * else, not empty it.
     */
    #[DataProvider('portals')]
    public function test_a_barber_passing_the_filter_is_ignored(string $p): void
    {
        $this->aNormalDay();

        foreach (['today', 'bookings'] as $screen) {
            foreach ([
                ['barber' => $this->zaid->id],
                ['barber' => 'unassigned'],
                ['barber' => 'all'],
                ['portal_role' => 'manager'],
                ['role' => 'manager'],
            ] as $query) {
                $body = $this->actingAs($this->adam, 'employee')
                    ->get(route($p.$screen, $query))
                    ->assertOk()
                    ->getContent();

                // Ignored in both directions: the parameter may not narrow
                // their own day away either. 'Alpha' is booked today, which
                // is upcoming, so it belongs on both screens.
                $this->assertStringContainsString('Alpha', $body, sprintf(
                    '%s lost the barber\'s own day to %s', $screen, json_encode($query)
                ));

                $this->assertStringNotContainsString('Bravo', $body, sprintf(
                    '%s leaked another barber with %s', $screen, json_encode($query)
                ));
                $this->assertStringNotContainsString('Charlie', $body, sprintf(
                    '%s leaked an unassigned booking with %s', $screen, json_encode($query)
                ));
                $this->assertStringNotContainsString('Zaid Abed', $body, sprintf(
                    '%s named another barber with %s', $screen, json_encode($query)
                ));
            }
        }

        // And their own day is still there, not narrowed away.
        $this->actingAs($this->adam, 'employee')
            ->get(route($p.'today', ['barber' => $this->zaid->id]))
            ->assertOk()
            ->assertSee('Alpha');
    }

    /**
     * The guard, read directly. ?barber= is dropped for anyone who is not a
     * manager before it is so much as looked at, so the screens below are
     * never filtered — which is also why nothing further down can misapply
     * it. Asserted on the resolved value rather than on the rendered page,
     * because a value that never reaches the view still reaches a query.
     */
    #[DataProvider('portals')]
    public function test_a_barber_is_never_filtered(string $p): void
    {
        $this->aNormalDay();

        foreach (['today', 'bookings'] as $screen) {
            foreach ([$this->zaid->id, 'unassigned', 'all', '', 'nonsense'] as $asked) {
                $response = $this->actingAs($this->adam, 'employee')
                    ->get(route($p.$screen, ['barber' => $asked]));

                $response->assertOk();

                $this->assertSame('all', $response->viewData('filter'), sprintf(
                    '%s resolved a filter for a barber from %s', $screen, var_export($asked, true)
                ));
            }
        }

        // And the same parameter does resolve, for the manager it belongs to.
        $this->assertSame(
            (string) $this->zaid->id,
            $this->actingAs($this->boss, 'employee')
                ->get(route($p.'bookings', ['barber' => $this->zaid->id]))
                ->viewData('filter')
        );
    }

    #[DataProvider('portals')]
    public function test_a_barber_gets_no_filter_chips_and_no_badge(string $p): void
    {
        $body = $this->actingAs($this->adam, 'employee')
            ->get(route($p.'today'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('p-chips', $body);
        $this->assertStringNotContainsString('p-badge-manager', $body);
        $this->assertStringNotContainsString('barber=', $body);
    }

    // ---------- chrome ----------

    #[DataProvider('portals')]
    public function test_the_header_shows_a_manager_badge(string $p): void
    {
        foreach (['today', 'bookings', 'earnings', 'profile'] as $screen) {
            $this->actingAs($this->boss, 'employee')
                ->get(route($p.$screen))
                ->assertOk()
                ->assertSee('p-badge-manager', false)
                ->assertSee('Manager');
        }
    }

    #[DataProvider('portals')]
    public function test_earnings_computes_nothing_for_a_manager_either(string $p): void
    {
        $this->actingAs($this->boss, 'employee')
            ->get(route($p.'earnings'))
            ->assertOk()
            ->assertSee('Coming soon')
            ->assertDontSee('$');
    }

    /**
     * Read-only, still. The only thing a manager can submit is a log out —
     * there is no assign, no cancel, no complete in this phase, and a screen
     * that grew one by accident would show up here as a second form action.
     */
    #[DataProvider('portals')]
    public function test_the_manager_portal_is_read_only(string $p): void
    {
        $this->aNormalDay();

        foreach (['today', 'bookings'] as $screen) {
            $body = $this->actingAs($this->boss, 'employee')
                ->get(route($p.$screen))
                ->assertOk()
                ->getContent();

            preg_match_all('/<form[^>]*action="([^"]*)"/', $body, $matches);

            $actions = array_values(array_unique(array_diff($matches[1], [route($p.'logout')])));

            $this->assertSame([], $actions, sprintf(
                'The %s %s screen posts somewhere other than logout: %s',
                $p, $screen, implode(', ', $actions)
            ));

            foreach (['Assign', 'Cancel booking', 'Mark complete'] as $action) {
                $this->assertStringNotContainsString($action, $body);
            }
        }
    }

    // ---------- the scope itself ----------

    public function test_visible_to_restricts_a_barber_and_frees_a_manager(): void
    {
        $this->aNormalDay();

        // A barber: their own, and not the unassigned one either.
        $this->assertSame(1, Booking::visibleTo($this->adam)->count());
        $this->assertSame(
            'Alpha Customer',
            Booking::visibleTo($this->adam)->first()->name
        );

        // A manager: everything, the unclaimed booking included.
        $this->assertSame(3, Booking::visibleTo($this->boss)->count());
    }

    public function test_visible_to_applies_to_pos_orders_the_same_way(): void
    {
        PosOrder::create([
            'order_number' => 'T-1', 'employee_id' => $this->adam->id, 'status' => 'completed',
        ]);
        PosOrder::create([
            'order_number' => 'T-2', 'employee_id' => $this->zaid->id, 'status' => 'completed',
        ]);

        $this->assertSame(1, PosOrder::visibleTo($this->adam)->count());
        $this->assertSame(2, PosOrder::visibleTo($this->boss)->count());
    }

    /**
     * The role is a value in a column, not a truthy flag. Anything that is not
     * exactly 'manager' must read as a barber, because the manager branch of
     * visibleTo() is the one that returns an unfiltered query.
     */
    public function test_only_the_exact_role_value_widens_what_is_visible(): void
    {
        $this->aNormalDay();

        foreach ([null, '', 'barber', 'Manager', 'managers', 'admin', '1', 'true'] as $value) {
            $this->adam->setRawAttributes(
                array_merge($this->adam->getAttributes(), ['portal_role' => $value])
            );

            $this->assertFalse(
                $this->adam->isManager(),
                var_export($value, true).' was treated as the manager role.'
            );

            $this->assertSame(
                1,
                Booking::visibleTo($this->adam)->count(),
                var_export($value, true).' widened what a barber can see.'
            );
        }
    }

    public function test_an_employee_is_a_barber_unless_made_otherwise(): void
    {
        $fresh = Employee::factory()->create();

        $this->assertSame(Employee::ROLE_BARBER, $fresh->fresh()->portal_role);
        $this->assertFalse($fresh->fresh()->isManager());
    }
}
