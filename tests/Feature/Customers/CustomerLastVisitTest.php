<?php

namespace Tests\Feature\Customers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Services\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * last_visit_at: when they were last in the chair, as opposed to
 * last_booked_at, which is when they last placed a booking.
 *
 * The two come apart the moment anyone books ahead, which is most people.
 */
class CustomerLastVisitTest extends TestCase
{
    use RefreshDatabase;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->travelTo('2026-11-10 14:00:00');

        $this->service = Service::create(['name' => 'Haircut', 'price' => 30]);
    }

    private function customer(array $attributes = []): Customer
    {
        return Customer::factory()->create($attributes + ['phone' => '2246698063']);
    }

    private function booking(Customer $c, string $date, string $time, string $status = 'completed'): Booking
    {
        return Booking::factory()->create([
            'customer_id' => $c->id,
            'service_id' => $this->service->id,
            'date' => $date,
            'time' => $time,
            'status' => $status,
        ]);
    }

    private function refresh(Customer $c): Customer
    {
        app(CustomerService::class)->refreshTotals($c);

        return $c->fresh();
    }

    // ---------- what it is ----------

    public function test_it_is_the_most_recent_appointment_that_has_already_happened(): void
    {
        $c = $this->customer();

        $this->booking($c, '2026-09-01', '10:00:00');
        $this->booking($c, '2026-10-15', '16:20:00');
        $this->booking($c, '2026-08-20', '09:00:00');

        $this->assertSame('2026-10-15 16:20:00', $this->refresh($c)->last_visit_at->format('Y-m-d H:i:s'));
    }

    public function test_an_appointment_still_to_come_is_not_a_visit(): void
    {
        $c = $this->customer();

        $this->booking($c, '2026-12-30', '10:00:00', 'pending');

        $this->assertNull($this->refresh($c)->last_visit_at);
    }

    public function test_a_cancelled_appointment_is_not_a_visit(): void
    {
        $c = $this->customer();

        $this->booking($c, '2026-10-15', '16:20:00', 'cancelled');
        $this->booking($c, '2026-09-01', '10:00:00', 'completed');

        $this->assertSame('2026-09-01 10:00:00', $this->refresh($c)->last_visit_at->format('Y-m-d H:i:s'));
    }

    public function test_a_customer_who_has_only_ever_booked_ahead_has_no_last_visit(): void
    {
        $c = $this->customer();

        $this->booking($c, '2026-11-20', '10:00:00', 'confirmed');
        $this->booking($c, '2026-12-01', '10:00:00', 'pending');

        $this->assertNull($this->refresh($c)->last_visit_at);
    }

    /**
     * The time of day decides, not the date alone. A haircut at ten this
     * morning is a visit by two this afternoon.
     */
    public function test_an_appointment_earlier_today_counts_and_a_later_one_does_not(): void
    {
        $c = $this->customer();

        $this->booking($c, '2026-11-10', '10:00:00');

        $this->assertSame('2026-11-10 10:00:00', $this->refresh($c)->last_visit_at->format('Y-m-d H:i:s'));

        $later = $this->booking($c, '2026-11-10', '18:40:00', 'confirmed');

        // Still this morning's: the evening appointment has not happened.
        $this->assertSame('2026-11-10 10:00:00', $this->refresh($c)->last_visit_at->format('Y-m-d H:i:s'));

        $this->travelTo('2026-11-10 19:30:00');

        $this->assertSame('2026-11-10 18:40:00', $this->refresh($c)->last_visit_at->format('Y-m-d H:i:s'));
        $this->assertNotNull($later->fresh());
    }

    // ---------- the two columns are different ----------

    public function test_last_booked_at_and_last_visit_at_answer_different_questions(): void
    {
        $c = $this->customer();

        // Booked this afternoon, for a fortnight's time.
        $booking = $this->booking($c, '2026-11-24', '10:00:00', 'confirmed');
        $booking->forceFill(['created_at' => '2026-11-10 14:00:00'])->save();

        // ...and was last actually in during September.
        $this->booking($c, '2026-09-01', '10:00:00');

        $fresh = $this->refresh($c);

        $this->assertSame('2026-11-10 14:00:00', $fresh->last_booked_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-01 10:00:00', $fresh->last_visit_at->format('Y-m-d H:i:s'));
    }

    // ---------- set on submit ----------

    /**
     * Submitting a booking sets the customer's last visit from their history.
     *
     * Worth being precise about what "on submit when the date is already
     * past" can mean here: the public form cannot create a past appointment
     * at all. `after_or_equal:today` refuses an earlier date, and the slot
     * service refuses a time earlier today — a 10am slot is not offered at
     * 2pm. So the new booking is never itself a visit.
     *
     * What submit does do is recompute the column, which is how a customer
     * who has been in before gets a last_visit_at the moment they book again
     * rather than waiting for the next nightly sweep.
     */
    public function test_submitting_a_booking_sets_the_last_visit_from_their_history(): void
    {
        $c = $this->customer();

        // They were in last month. Nothing has written the column yet.
        $this->booking($c, '2026-10-15', '16:20:00');
        $this->assertNull($c->fresh()->last_visit_at);

        // Now they book their next one through the form.
        $this->postJson(route('bookings.store'), [
            'first_name' => 'Omar',
            'phone' => '2246698063',
            'date' => '2026-12-30',
            'time' => '10:00',
            'service_id' => $this->service->id,
        ])->assertOk();

        $this->assertSame(
            '2026-10-15 16:20:00',
            $c->fresh()->last_visit_at->format('Y-m-d H:i:s'),
            'Submitting a booking did not refresh the last visit.'
        );
    }

    /** And the booking they just made is not itself a visit. */
    public function test_booking_ahead_leaves_the_last_visit_alone(): void
    {
        $this->postJson(route('bookings.store'), [
            'first_name' => 'Omar',
            'phone' => '2246698063',
            'date' => '2026-12-30',
            'time' => '10:00',
            'service_id' => $this->service->id,
        ])->assertOk();

        $customer = Customer::sole();

        $this->assertNull($customer->last_visit_at);
        $this->assertNotNull($customer->last_booked_at);
    }

    // ---------- the nightly sweep ----------

    /**
     * The reason a sweep is needed at all: nothing happens when an
     * appointment arrives. The booking was written weeks ago and nobody
     * touches the row on the day.
     */
    public function test_the_sweep_moves_a_visit_that_has_since_happened(): void
    {
        $c = $this->customer();

        // Booked ahead, so nothing was a visit at the time.
        $this->booking($c, '2026-11-20', '10:00:00', 'confirmed');
        $this->refresh($c);

        $this->assertNull($c->fresh()->last_visit_at);

        // The day comes and goes. Nothing has touched the customer.
        $this->travelTo('2026-11-21 03:05:00');

        $this->artisan('bookings:send-reminders')->assertSuccessful();

        $this->assertSame('2026-11-20 10:00:00', $c->fresh()->last_visit_at->format('Y-m-d H:i:s'));
    }

    public function test_the_sweep_runs_once_a_day_and_not_before_three(): void
    {
        $c = $this->customer();
        $this->booking($c, '2026-11-09', '10:00:00');

        // Too early: the command still sends reminders, and sweeps nothing.
        $this->travelTo('2026-11-10 01:30:00');
        $this->artisan('bookings:send-reminders')->assertSuccessful();
        $this->assertNull($c->fresh()->last_visit_at);
        $this->assertNull(Cache::get('customers.last_visit_swept_on'));

        // After three, it runs.
        $this->travelTo('2026-11-10 03:01:00');
        $this->artisan('bookings:send-reminders')->assertSuccessful();
        $this->assertSame('2026-11-09 10:00:00', $c->fresh()->last_visit_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-11-10', Cache::get('customers.last_visit_swept_on'));

        // And not again today, however many times the minute ticks.
        $c->forceFill(['last_visit_at' => null])->save();
        $this->travelTo('2026-11-10 03:02:00');
        $this->artisan('bookings:send-reminders')->assertSuccessful();
        $this->assertNull($c->fresh()->last_visit_at, 'The sweep ran twice in one day.');

        // Tomorrow it does.
        $this->travelTo('2026-11-11 03:01:00');
        $this->artisan('bookings:send-reminders')->assertSuccessful();
        $this->assertSame('2026-11-09 10:00:00', $c->fresh()->last_visit_at->format('Y-m-d H:i:s'));
    }

    public function test_the_sweep_clears_a_visit_that_was_cancelled_after_the_fact(): void
    {
        $c = $this->customer();
        $booking = $this->booking($c, '2026-11-09', '10:00:00');

        $this->travelTo('2026-11-10 03:01:00');
        $this->artisan('bookings:send-reminders')->assertSuccessful();
        $this->assertNotNull($c->fresh()->last_visit_at);

        // They did not turn up, and the shop cancelled it afterwards.
        $booking->forceFill(['status' => 'cancelled'])->save();

        $this->travelTo('2026-11-11 03:01:00');
        $this->artisan('bookings:send-reminders')->assertSuccessful();

        $this->assertNull($c->fresh()->last_visit_at, 'A cancelled appointment is still counted as a visit.');
    }

    public function test_the_sweep_leaves_everyone_else_alone(): void
    {
        $visited = Customer::factory()->create(['phone' => '2246698001']);
        $ahead = Customer::factory()->create(['phone' => '2246698002']);
        $never = Customer::factory()->create(['phone' => '2246698003']);

        $this->booking($visited, '2026-11-01', '10:00:00');
        $this->booking($ahead, '2026-12-20', '10:00:00', 'confirmed');

        $this->travelTo('2026-11-10 03:01:00');
        $this->artisan('bookings:send-reminders')->assertSuccessful();

        $this->assertSame('2026-11-01 10:00:00', $visited->fresh()->last_visit_at->format('Y-m-d H:i:s'));
        $this->assertNull($ahead->fresh()->last_visit_at);
        $this->assertNull($never->fresh()->last_visit_at);
    }

    /** A sweep that fails must never stop a reminder going out. */
    public function test_a_broken_sweep_does_not_fail_the_command(): void
    {
        Cache::shouldReceive('get')->andThrow(new \RuntimeException('cache is gone'));

        $this->travelTo('2026-11-10 03:01:00');

        $this->artisan('bookings:send-reminders')->assertSuccessful();
    }
}
