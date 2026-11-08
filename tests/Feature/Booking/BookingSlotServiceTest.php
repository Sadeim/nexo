<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Employee;
use App\Services\BookingSlotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The capacity rule is the only thing standing between a customer and a slot
 * that cannot actually be served, so it is tested directly rather than through
 * the booking form.
 *
 * Lives in Feature rather than Unit because the rule counts rows: it reads
 * published barbers and existing bookings. The repo keeps tests/Unit
 * framework-free, and pretending this one is isolated would mean mocking the
 * two queries that make it worth testing.
 *
 * Dates are fixed and their weekday asserted, because the shop keeps different
 * hours on Sunday and a floating "tomorrow" would silently change what is being
 * tested.
 */
class BookingSlotServiceTest extends TestCase
{
    use RefreshDatabase;

    private const WEDNESDAY = '2026-11-11';
    private const SUNDAY    = '2026-11-15';

    private BookingSlotService $slots;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slots = new BookingSlotService();

        // Fix the clock well before the Wednesday under test so no slot counts
        // as already started.
        $this->travelTo('2026-11-10 08:00:00');
    }

    private function publishBarbers(int $count): array
    {
        return Employee::factory()->count($count)->published()->create()->all();
    }

    // ---------- capacity ----------

    public function test_capacity_is_the_number_of_published_barbers(): void
    {
        $this->publishBarbers(3);

        $this->assertSame(3, $this->slots->capacity());
    }

    public function test_capacity_falls_back_to_one_when_nobody_is_published(): void
    {
        // An employee who exists but is not published must not add capacity —
        // otherwise setting up the POS would quietly open extra chairs.
        Employee::factory()->count(4)->create();

        $this->assertSame(1, $this->slots->capacity());
    }

    public function test_capacity_ignores_inactive_and_unpublished_barbers(): void
    {
        $this->publishBarbers(2);
        Employee::factory()->published()->inactive()->create();
        Employee::factory()->create();

        $this->assertSame(2, $this->slots->capacity());
    }

    // ---------- "any available barber" ----------

    public function test_any_barber_slot_is_open_until_capacity_is_reached(): void
    {
        $this->publishBarbers(2);

        $this->assertTrue($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '10:00'));

        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();
        $this->assertTrue(
            $this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '10:00'),
            'One booking against a capacity of two must leave the slot open.'
        );

        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();
        $this->assertFalse(
            $this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '10:00'),
            'A full shop must refuse an unassigned booking.'
        );
    }

    public function test_single_capacity_behaves_as_before_barbers_existed(): void
    {
        Booking::factory()->at(self::WEDNESDAY, '11:00:00')->create();

        $this->assertSame(1, $this->slots->capacity());
        $this->assertFalse($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '11:00'));
        $this->assertTrue($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '11:20'));
    }

    // ---------- a named barber ----------

    public function test_named_barber_cannot_be_booked_twice_in_one_slot(): void
    {
        [$omar, $sami] = $this->publishBarbers(2);

        Booking::factory()->at(self::WEDNESDAY, '12:00:00')->create(['barber_id' => $omar->id]);

        $this->assertFalse(
            $this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '12:00', $omar->id),
            'Omar is already busy, even though the shop has a free chair.'
        );
        $this->assertTrue(
            $this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '12:00', $sami->id),
            'Sami is free and the shop is not full.'
        );
    }

    public function test_named_barber_is_refused_when_the_shop_is_full(): void
    {
        [$omar, $sami] = $this->publishBarbers(2);

        // Both chairs taken by unassigned bookings, so even a barber with no
        // booking of their own has nowhere to work.
        Booking::factory()->at(self::WEDNESDAY, '13:00:00')->count(2)->create();

        $this->assertFalse($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '13:00', $omar->id));
        $this->assertFalse($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '13:00', $sami->id));
    }

    public function test_a_barbers_booking_does_not_block_a_different_slot(): void
    {
        [$omar] = $this->publishBarbers(2);

        Booking::factory()->at(self::WEDNESDAY, '14:00:00')->create(['barber_id' => $omar->id]);

        $this->assertTrue($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '14:20', $omar->id));
    }

    // ---------- cancellation ----------

    public function test_a_cancelled_booking_frees_the_slot(): void
    {
        [$omar] = $this->publishBarbers(1);

        Booking::factory()->at(self::WEDNESDAY, '15:00:00')
            ->cancelled()
            ->create(['barber_id' => $omar->id]);

        $this->assertTrue($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '15:00', $omar->id));
        $this->assertTrue($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '15:00'));
    }

    public function test_a_confirmed_booking_still_occupies_the_slot(): void
    {
        $this->publishBarbers(1);

        Booking::factory()->at(self::WEDNESDAY, '15:40:00')->create(['status' => 'confirmed']);

        $this->assertFalse($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '15:40'));
    }

    // ---------- off-grid bookings ----------

    public function test_an_off_grid_booking_blocks_the_slots_it_overlaps(): void
    {
        // The dashboard can create a booking at any time, not just on the
        // 20-minute grid. It still ties up a chair across what it overlaps.
        Booking::factory()->at(self::WEDNESDAY, '16:10:00')->create();

        $this->assertFalse($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '16:00'));
        $this->assertFalse($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '16:20'));
        $this->assertTrue($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '16:40'));
    }

    // ---------- opening hours ----------

    public function test_weekday_window_rejects_times_outside_opening_hours(): void
    {
        $this->assertFalse($this->slots->isTimeInWorkingHours(self::WEDNESDAY, '08:40'));
        $this->assertTrue($this->slots->isTimeInWorkingHours(self::WEDNESDAY, '09:00'));
        $this->assertTrue($this->slots->isTimeInWorkingHours(self::WEDNESDAY, '19:40'));
        $this->assertFalse($this->slots->isTimeInWorkingHours(self::WEDNESDAY, '20:00'));
    }

    public function test_sunday_keeps_shorter_hours(): void
    {
        $this->assertFalse($this->slots->isTimeInWorkingHours(self::SUNDAY, '09:00'));
        $this->assertTrue($this->slots->isTimeInWorkingHours(self::SUNDAY, '10:00'));
        $this->assertTrue($this->slots->isTimeInWorkingHours(self::SUNDAY, '16:40'));
        $this->assertFalse($this->slots->isTimeInWorkingHours(self::SUNDAY, '17:00'));
    }

    public function test_a_time_outside_opening_hours_is_never_bookable(): void
    {
        $this->publishBarbers(3);

        $this->assertFalse($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '21:00'));
    }

    public function test_shop_hours_are_derived_from_the_service_constants(): void
    {
        $hours = $this->slots->shopHours();

        $this->assertSame('Monday – Saturday', $hours[0]['days']);
        $this->assertSame('9:00 AM', $hours[0]['open']);
        // Last slot starts 7:40 PM and runs 20 minutes, so the shop closes at 8.
        $this->assertSame('8:00 PM', $hours[0]['close']);

        $this->assertSame('Sunday', $hours[1]['days']);
        $this->assertSame('10:00 AM', $hours[1]['open']);
        $this->assertSame('5:00 PM', $hours[1]['close']);
    }

    // ---------- the slot list ----------

    public function test_slot_list_covers_the_whole_day_on_the_grid(): void
    {
        $slots = $this->slots->getAvailableSlots(self::WEDNESDAY);

        // 09:00 to 19:40 inclusive, every 20 minutes.
        $this->assertCount(33, $slots);
        $this->assertSame('09:00', $slots[0]['value']);
        $this->assertSame('9:00 AM', $slots[0]['label']);
        $this->assertSame('19:40', end($slots)['value']);
    }

    public function test_taken_slots_are_returned_disabled_not_omitted(): void
    {
        $this->publishBarbers(1);
        Booking::factory()->at(self::WEDNESDAY, '09:20:00')->create();

        $slots = collect($this->slots->getAvailableSlots(self::WEDNESDAY))->keyBy('value');

        $this->assertTrue($slots->has('09:20'), 'A taken slot must still be listed.');
        $this->assertTrue($slots['09:20']['disabled']);
        $this->assertFalse($slots['09:00']['disabled']);
    }

    public function test_slot_list_is_disabled_per_barber(): void
    {
        [$omar, $sami] = $this->publishBarbers(2);
        Booking::factory()->at(self::WEDNESDAY, '09:40:00')->create(['barber_id' => $omar->id]);

        $forOmar = collect($this->slots->getAvailableSlots(self::WEDNESDAY, $omar->id))->keyBy('value');
        $forSami = collect($this->slots->getAvailableSlots(self::WEDNESDAY, $sami->id))->keyBy('value');
        $forAny  = collect($this->slots->getAvailableSlots(self::WEDNESDAY))->keyBy('value');

        $this->assertTrue($forOmar['09:40']['disabled'], 'Omar is busy at 9:40.');
        $this->assertFalse($forSami['09:40']['disabled'], 'Sami is not.');
        $this->assertFalse($forAny['09:40']['disabled'], 'One of two chairs is still free.');
    }

    public function test_past_slots_are_omitted_today_but_not_on_a_later_day(): void
    {
        $this->travelTo('2026-11-11 12:10:00');

        $today = collect($this->slots->getAvailableSlots(self::WEDNESDAY))->pluck('value');

        $this->assertFalse($today->contains('09:00'), 'A slot that has passed is gone, not disabled.');
        $this->assertFalse($today->contains('12:00'));
        $this->assertTrue($today->contains('12:20'));

        $later = collect($this->slots->getAvailableSlots('2026-11-12'))->pluck('value');
        $this->assertTrue($later->contains('09:00'));
    }

    public function test_a_slot_that_has_already_started_cannot_be_booked(): void
    {
        $this->travelTo('2026-11-11 12:10:00');

        $this->assertFalse(
            $this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '09:00'),
            'The server must refuse a past slot even if a stale page offers it.'
        );
        $this->assertTrue($this->slots->isSlotValidAndAvailable(self::WEDNESDAY, '12:20'));
    }

    public function test_the_fixed_dates_really_are_the_weekdays_the_tests_assume(): void
    {
        $this->assertSame('Wednesday', date('l', strtotime(self::WEDNESDAY)));
        $this->assertSame('Sunday', date('l', strtotime(self::SUNDAY)));
    }
}
