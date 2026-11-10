<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Employee;
use App\Mail\BookingConfirmationMail;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The public booking endpoints, over HTTP.
 *
 * BookingSlotServiceTest proves the rule and SlotUniquenessTest proves the
 * constraint; these assert the endpoints actually apply them, and that a
 * customer who loses a slot is told so rather than shown a 500.
 */
class StoreBookingTest extends TestCase
{
    use RefreshDatabase;

    private const WEDNESDAY = '2026-11-11';

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->travelTo('2026-11-10 08:00:00');

        $this->service = Service::create(['name' => 'Haircut', 'price' => 30]);
    }

    private function barber(): Employee
    {
        return Employee::factory()->published()->create();
    }

    /**
     * What the modal posts since 10 Nov 2026: a phone and a first name are
     * required, the name is two fields, and the email is optional.
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Walk',
            'last_name'  => 'In',
            'phone'      => '2246698063',
            'email'      => 'walkin@example.com',
            'date'       => self::WEDNESDAY,
            'time'       => '10:00',
            'service_id' => $this->service->id,
        ], $overrides);
    }

    // ---------- booking without a barber ----------

    public function test_a_booking_can_be_made_without_naming_a_barber(): void
    {
        $response = $this->postJson(route('bookings.store'), $this->payload());

        $response->assertOk()->assertJson(['success' => true]);

        $booking = Booking::first();
        $this->assertNull($booking->barber_id, 'No barber named means any barber.');
        $this->assertNull($booking->slot_key, 'An unassigned booking claims nobody\'s slot.');
        $this->assertSame('10:00:00', $booking->time);
    }

    public function test_a_full_shop_refuses_an_unassigned_booking(): void
    {
        // One published barber, so capacity is 1 and one booking fills it.
        $this->barber();
        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();

        $response = $this->postJson(route('bookings.store'), $this->payload());

        $response->assertStatus(422)->assertJson(['success' => false]);
        $response->assertJsonFragment(['message' => 'The selected time is not available. Please choose another slot.']);
        $this->assertSame(1, Booking::count(), 'The refused booking must not be stored.');
    }

    public function test_capacity_lets_a_second_customer_book_the_same_time(): void
    {
        $this->barber();
        $this->barber();

        $this->postJson(route('bookings.store'), $this->payload())->assertOk();
        $this->postJson(route('bookings.store'), $this->payload())->assertOk();
        $this->postJson(route('bookings.store'), $this->payload())->assertStatus(422);

        $this->assertSame(2, Booking::count());
    }

    // ---------- booking with a barber ----------

    public function test_a_booking_can_name_a_published_barber(): void
    {
        $barber = $this->barber();

        $this->postJson(route('bookings.store'), $this->payload(['barber_id' => $barber->id]))
            ->assertOk();

        $booking = Booking::first();
        $this->assertSame($barber->id, $booking->barber_id);
        $this->assertSame(self::WEDNESDAY . '|10:00:00|' . $barber->id, $booking->slot_key);
    }

    public function test_a_barber_who_is_already_booked_is_refused(): void
    {
        $barber = $this->barber();
        $this->barber(); // capacity 2, so the shop itself is not full

        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create(['barber_id' => $barber->id]);

        $response = $this->postJson(route('bookings.store'), $this->payload(['barber_id' => $barber->id]));

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'That barber is already booked at this time. Please choose another slot.']);
        $this->assertSame(1, Booking::count());
    }

    public function test_an_unpublished_barber_cannot_be_requested(): void
    {
        $hidden = Employee::factory()->create(); // active but not published

        $this->postJson(route('bookings.store'), $this->payload(['barber_id' => $hidden->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('barber_id');
    }

    public function test_an_inactive_barber_cannot_be_requested(): void
    {
        $gone = Employee::factory()->published()->inactive()->create();

        $this->postJson(route('bookings.store'), $this->payload(['barber_id' => $gone->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('barber_id');
    }

    // ---------- the rule still applies ----------

    public function test_a_time_outside_opening_hours_is_refused(): void
    {
        $this->postJson(route('bookings.store'), $this->payload(['time' => '21:00']))
            ->assertStatus(422);

        $this->assertSame(0, Booking::count());
    }

    public function test_a_slot_that_has_already_passed_is_refused(): void
    {
        $this->travelTo('2026-11-11 15:00:00');

        $this->postJson(route('bookings.store'), $this->payload(['time' => '10:00']))
            ->assertStatus(422);
    }

    public function test_a_cancelled_booking_leaves_the_slot_bookable(): void
    {
        $barber = $this->barber();

        Booking::factory()->at(self::WEDNESDAY, '10:00:00')
            ->cancelled()
            ->create(['barber_id' => $barber->id]);

        $this->postJson(route('bookings.store'), $this->payload(['barber_id' => $barber->id]))
            ->assertOk();
    }

    // ---------- the slots endpoint ----------

    public function test_the_slots_endpoint_flags_taken_slots_as_disabled(): void
    {
        $this->barber();
        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();

        $response = $this->getJson(route('bookings.booked-slots', ['date' => self::WEDNESDAY]));

        $response->assertOk()->assertJson(['success' => true]);

        $slots = collect($response->json('slots'))->keyBy('value');
        $this->assertTrue($slots['10:00']['disabled'], 'A taken slot is listed but disabled.');
        $this->assertFalse($slots['10:20']['disabled']);
        $this->assertArrayHasKey('label', $slots['10:00']);
    }

    public function test_the_slots_endpoint_narrows_to_one_barber(): void
    {
        $omar = $this->barber();
        $sami = $this->barber();

        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create(['barber_id' => $omar->id]);

        $forOmar = collect($this->getJson(route('bookings.booked-slots', [
            'date' => self::WEDNESDAY, 'barber_id' => $omar->id,
        ]))->json('slots'))->keyBy('value');

        $forSami = collect($this->getJson(route('bookings.booked-slots', [
            'date' => self::WEDNESDAY, 'barber_id' => $sami->id,
        ]))->json('slots'))->keyBy('value');

        $this->assertTrue($forOmar['10:00']['disabled']);
        $this->assertFalse($forSami['10:00']['disabled']);
    }

    public function test_the_slots_endpoint_returns_nothing_without_a_date(): void
    {
        $this->getJson(route('bookings.booked-slots'))
            ->assertOk()
            ->assertJson(['success' => true, 'slots' => []]);
    }

    public function test_the_slots_endpoint_rejects_an_unpublished_barber(): void
    {
        $hidden = Employee::factory()->create();

        $this->getJson(route('bookings.booked-slots', [
            'date' => self::WEDNESDAY, 'barber_id' => $hidden->id,
        ]))->assertStatus(422)->assertJsonValidationErrors('barber_id');
    }

    // ---------- confirmation email ----------

    public function test_the_confirmation_email_names_the_barber(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $this->postJson(route('bookings.store'), $this->payload(['barber_id' => $barber->id]))
            ->assertOk();

        Mail::assertSent(BookingConfirmationMail::class, function (BookingConfirmationMail $mail) {
            return str_contains($mail->render(), 'Omar Haddad');
        });
    }

    public function test_the_confirmation_email_says_any_available_barber_when_none_was_chosen(): void
    {
        $this->postJson(route('bookings.store'), $this->payload())->assertOk();

        Mail::assertSent(BookingConfirmationMail::class, function (BookingConfirmationMail $mail) {
            return str_contains($mail->render(), 'Any available barber');
        });
    }
}
