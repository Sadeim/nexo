<?php

namespace Tests\Feature\Booking;

use App\Exceptions\SlotUnavailableException;
use App\Models\Booking;
use App\Models\Employee;
use App\Services\BookingSlotService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The database half of the availability rule.
 *
 * BookingSlotService decides availability in PHP, which is a read followed by
 * a write — two requests can both pass the read. These tests do not simulate
 * threads; they do something stricter. They skip the rule entirely and write
 * straight to the table, which is what a lost race amounts to, and assert the
 * database refuses the second write on its own.
 */
class SlotUniquenessTest extends TestCase
{
    use RefreshDatabase;

    private const WEDNESDAY = '2026-11-11';

    private BookingSlotService $slots;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slots = new BookingSlotService();
        $this->travelTo('2026-11-10 08:00:00');
    }

    private function barber(): Employee
    {
        return Employee::factory()->published()->create();
    }

    private function book(array $attributes = []): Booking
    {
        return Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create($attributes);
    }

    // ---------- the constraint ----------

    public function test_only_one_of_two_competing_writes_for_the_same_barber_survives(): void
    {
        $barber = $this->barber();

        $this->book(['barber_id' => $barber->id]);

        // The second write is what a request that lost the race does: it
        // passed the rule a moment ago and is now inserting anyway.
        $this->expectException(QueryException::class);
        $this->book(['barber_id' => $barber->id]);
    }

    public function test_exactly_one_row_exists_after_a_collision(): void
    {
        $barber = $this->barber();
        $survivors = 0;

        foreach (range(1, 4) as $attempt) {
            try {
                $this->book(['barber_id' => $barber->id]);
                $survivors++;
            } catch (QueryException $e) {
                // Expected for every attempt after the first.
            }
        }

        $this->assertSame(1, $survivors);
        $this->assertSame(1, Booking::where('barber_id', $barber->id)->count());
    }

    public function test_a_collision_is_reported_as_an_unavailable_slot(): void
    {
        $barber = $this->barber();
        $this->book(['barber_id' => $barber->id]);

        $this->expectException(SlotUnavailableException::class);
        $this->expectExceptionMessage('already booked');

        $this->slots->guardSlotUniqueness(
            fn () => $this->book(['barber_id' => $barber->id])
        );
    }

    public function test_the_guard_passes_through_an_unrelated_database_error(): void
    {
        // A failure that is not our constraint must not be dressed up as a
        // busy barber — that would hide real bugs behind a friendly message.
        $this->expectException(QueryException::class);

        $this->slots->guardSlotUniqueness(
            fn () => Booking::create(['name' => 'No date or time given'])
        );
    }

    // ---------- NULL keys ----------

    public function test_unassigned_bookings_never_collide(): void
    {
        $this->barber();

        // "Any available barber" claims nobody's slot, so the shop's capacity
        // is the only limit — the index must not interfere.
        $this->book();
        $this->book();
        $this->book();

        $this->assertSame(3, Booking::whereNull('barber_id')->count());
        $this->assertSame(3, Booking::whereNull('slot_key')->count());
    }

    public function test_two_barbers_can_hold_the_same_slot(): void
    {
        $omar = $this->barber();
        $sami = $this->barber();

        $this->book(['barber_id' => $omar->id]);
        $this->book(['barber_id' => $sami->id]);

        $this->assertSame(2, Booking::whereNotNull('slot_key')->count());
    }

    public function test_cancelling_frees_the_slot_immediately(): void
    {
        $barber = $this->barber();
        $first  = $this->book(['barber_id' => $barber->id]);

        $first->update(['status' => Booking::STATUS_CANCELLED]);

        $this->assertNull($first->fresh()->slot_key, 'A cancelled booking must release its key.');

        // And the freed slot is immediately rebookable by the same barber.
        $second = $this->book(['barber_id' => $barber->id]);
        $this->assertNotNull($second->slot_key);
    }

    // ---------- the observer owns the key ----------

    public function test_the_key_is_set_on_create_without_anyone_asking(): void
    {
        $barber = $this->barber();
        $booking = $this->book(['barber_id' => $barber->id]);

        $this->assertSame(
            self::WEDNESDAY . '|10:00:00|' . $barber->id,
            $booking->slot_key
        );
    }

    public function test_the_key_follows_a_reassignment(): void
    {
        $omar = $this->barber();
        $sami = $this->barber();

        $booking = $this->book(['barber_id' => $omar->id]);
        $booking->update(['barber_id' => $sami->id]);

        $this->assertSame(
            self::WEDNESDAY . '|10:00:00|' . $sami->id,
            $booking->fresh()->slot_key
        );
    }

    public function test_the_key_follows_a_time_change(): void
    {
        $barber  = $this->barber();
        $booking = $this->book(['barber_id' => $barber->id]);

        $booking->update(['time' => '11:20:00']);

        $this->assertSame(
            self::WEDNESDAY . '|11:20:00|' . $barber->id,
            $booking->fresh()->slot_key
        );
    }

    public function test_the_key_is_cleared_when_a_booking_becomes_unassigned(): void
    {
        $barber  = $this->barber();
        $booking = $this->book(['barber_id' => $barber->id]);

        $booking->update(['barber_id' => null]);

        $this->assertNull($booking->fresh()->slot_key);
    }

    public function test_the_same_appointment_written_two_ways_still_collides(): void
    {
        $barber = $this->barber();

        // '10:00' and '10:00:00' are one appointment. If the key were built
        // from the raw column the index would wave the second one through.
        Booking::factory()->at(self::WEDNESDAY, '10:00')->create(['barber_id' => $barber->id]);

        $this->expectException(QueryException::class);
        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create(['barber_id' => $barber->id]);
    }

    public function test_slot_key_cannot_be_set_through_mass_assignment(): void
    {
        $barber = $this->barber();

        $booking = $this->book([
            'barber_id' => $barber->id,
            'slot_key'  => 'forged|value|999',
        ]);

        $this->assertSame(
            self::WEDNESDAY . '|10:00:00|' . $barber->id,
            $booking->slot_key,
            'The observer must overwrite anything a caller tries to inject.'
        );
    }

    // ---------- admin assignment ----------

    public function test_assigning_an_unassigned_booking_to_a_free_barber_works(): void
    {
        $barber  = $this->barber();
        $pending = $this->book();

        $this->assertNull($pending->slot_key);

        $this->slots->guardSlotUniqueness(
            fn () => $pending->update(['barber_id' => $barber->id])
        );

        $this->assertSame(
            self::WEDNESDAY . '|10:00:00|' . $barber->id,
            $pending->fresh()->slot_key
        );
    }

    public function test_assigning_a_booking_to_an_already_booked_barber_is_refused(): void
    {
        $barber = $this->barber();

        $this->book(['barber_id' => $barber->id]);
        $unassigned = $this->book();

        $this->expectException(SlotUnavailableException::class);
        $this->expectExceptionMessage('already booked');

        $this->slots->guardSlotUniqueness(
            fn () => $unassigned->update(['barber_id' => $barber->id])
        );
    }

    public function test_a_refused_assignment_leaves_the_booking_untouched(): void
    {
        $barber = $this->barber();

        $this->book(['barber_id' => $barber->id]);
        $unassigned = $this->book();

        try {
            $this->slots->guardSlotUniqueness(
                fn () => $unassigned->update(['barber_id' => $barber->id])
            );
        } catch (SlotUnavailableException $e) {
            // Expected.
        }

        $fresh = $unassigned->fresh();
        $this->assertNull($fresh->barber_id, 'A rejected assignment must not stick.');
        $this->assertNull($fresh->slot_key);
    }
}
