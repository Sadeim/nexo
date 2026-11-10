<?php

namespace Tests\Feature\Customers;

use App\Mail\AdminBookingNotification;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Jobs\SendBookingReminderJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Step two: the booking itself, and the customer it belongs to.
 */
class BookingCustomerLinkTest extends TestCase
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

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Omar',
            'last_name' => 'Haddad',
            'phone' => '(224) 669-8063',
            'email' => 'omar@example.com',
            'date' => self::WEDNESDAY,
            'time' => '10:00',
            'service_id' => $this->service->id,
        ], $overrides);
    }

    private function book(array $overrides = [])
    {
        return $this->postJson(route('bookings.store'), $this->payload($overrides));
    }

    // ---------- linking ----------

    public function test_a_booking_creates_and_links_its_customer(): void
    {
        $this->book()->assertOk();

        $customer = Customer::sole();

        $this->assertSame('2246698063', $customer->phone);
        $this->assertSame('Omar', $customer->first_name);
        $this->assertSame('Haddad', $customer->last_name);
        $this->assertSame('omar@example.com', $customer->email);
        $this->assertSame(1, $customer->bookings_count);
        $this->assertSame($customer->id, Booking::sole()->customer_id);
    }

    public function test_the_booking_keeps_its_own_copy_of_what_was_typed(): void
    {
        $this->book()->assertOk();

        $booking = Booking::sole();

        // One name field, as it always was: the record of the day.
        $this->assertSame('Omar Haddad', $booking->name);
        $this->assertSame('2246698063', $booking->phone);
    }

    public function test_booking_twice_is_one_customer_with_two_bookings(): void
    {
        $this->book()->assertOk();
        $this->book(['time' => '11:00', 'phone' => '+1 224-669-8063'])->assertOk();

        $this->assertSame(1, Customer::count());

        $customer = Customer::sole();

        $this->assertSame(2, $customer->bookings_count);
        $this->assertSame(2, Booking::where('customer_id', $customer->id)->count());
    }

    public function test_last_booked_at_moves_with_the_latest_booking(): void
    {
        $this->travelTo('2026-11-10 09:00:00');
        $this->book()->assertOk();

        $this->travelTo('2026-11-10 14:30:00');
        $this->book(['time' => '11:00'])->assertOk();

        $this->assertSame(
            '2026-11-10 14:30:00',
            Customer::sole()->last_booked_at->format('Y-m-d H:i:s')
        );
    }

    public function test_a_returning_customer_who_corrects_their_name_keeps_one_record(): void
    {
        $this->book()->assertOk();
        $this->book(['time' => '11:00', 'first_name' => 'Omer', 'last_name' => 'Hadad'])->assertOk();

        $customer = Customer::sole();

        // The newer spelling wins; the earlier booking keeps what it said.
        $this->assertSame('Omer', $customer->first_name);
        $this->assertSame('Omar Haddad', Booking::orderBy('id')->first()->name);
    }

    /** Step one has already created the row; step two must find it. */
    public function test_a_booking_after_identify_does_not_make_a_second_customer(): void
    {
        $this->postJson(route('bookings.identify'), [
            'phone' => '224-669-8063',
            'sms_marketing_consent' => 1,
        ])->assertOk();

        $this->book()->assertOk();

        $this->assertSame(1, Customer::count());

        $customer = Customer::sole();

        // The consent given at step one survives step two.
        $this->assertTrue($customer->sms_marketing_consent);
        $this->assertSame('Omar', $customer->first_name);
        $this->assertSame(1, $customer->bookings_count);
    }

    // ---------- validation ----------

    public function test_a_phone_number_is_required(): void
    {
        $this->book(['phone' => null])->assertStatus(422)->assertJsonValidationErrors('phone');
        $this->book(['phone' => 'abc'])->assertStatus(422);

        $this->assertSame(0, Booking::count());
        $this->assertSame(0, Customer::count());
    }

    public function test_a_first_name_is_required_but_a_last_name_is_not(): void
    {
        $this->book(['first_name' => null])->assertStatus(422)->assertJsonValidationErrors('first_name');

        $this->book(['last_name' => null])->assertOk();

        $this->assertSame('Omar', Booking::sole()->name);
        $this->assertNull(Customer::sole()->last_name);
    }

    public function test_an_email_is_optional(): void
    {
        $this->book(['email' => null])->assertOk();

        $this->assertNull(Booking::sole()->email);
        $this->assertNull(Customer::sole()->email);
    }

    public function test_an_email_that_is_given_still_has_to_be_an_email(): void
    {
        $this->book(['email' => 'not-an-email'])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    // ---------- email, or the absence of one ----------

    public function test_a_booking_with_an_email_gets_both_messages(): void
    {
        $this->book()->assertOk();

        Mail::assertSent(BookingConfirmationMail::class);
        Mail::assertSent(AdminBookingNotification::class);
    }

    /**
     * The one that must not break. No address means no confirmation — and the
     * shop's own notification still has to arrive, which it did not when both
     * sends shared a try block.
     */
    public function test_a_booking_without_an_email_still_notifies_the_shop(): void
    {
        $this->book(['email' => null])->assertOk();

        Mail::assertNotSent(BookingConfirmationMail::class);
        Mail::assertSent(AdminBookingNotification::class);
    }

    public function test_the_reply_does_not_promise_an_email_that_is_not_coming(): void
    {
        $this->book(['email' => null])
            ->assertOk()
            ->assertJsonFragment(['success' => true]);

        $message = $this->book(['email' => null, 'time' => '11:00'])->json('message');

        $this->assertStringNotContainsString('email', strtolower($message));
    }

    /**
     * The reminder is dispatched for every booking, including one with no
     * address. Mail::to(null) throws, so the job has to decide not to send.
     */
    public function test_the_reminder_job_does_not_throw_on_a_booking_with_no_email(): void
    {
        $this->book(['email' => null])->assertOk();

        $booking = Booking::sole();

        (new SendBookingReminderJob($booking))->handle();

        Mail::assertNotSent(BookingReminderMail::class);
    }

    public function test_the_reminder_still_goes_out_when_there_is_an_address(): void
    {
        $this->book()->assertOk();

        (new SendBookingReminderJob(Booking::sole()))->handle();

        Mail::assertSent(BookingReminderMail::class);
    }

    /** The admin notification renders a booking that has no email. */
    public function test_the_admin_notification_renders_without_an_email(): void
    {
        $this->book(['email' => null])->assertOk();

        $booking = Booking::sole()->load(['service', 'barber']);

        $rendered = (new AdminBookingNotification($booking))->render();

        $this->assertStringContainsString('(224) 669-8063', $rendered);
        $this->assertStringContainsString('—', $rendered);
    }
}
