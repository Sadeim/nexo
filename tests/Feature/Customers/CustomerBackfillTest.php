<?php

namespace Tests\Feature\Customers;

use App\Models\Booking;
use App\Models\Customer;
use App\Services\CustomerBackfill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Turning the bookings already in the table into customers.
 *
 * On nexobarbers.com this finds nothing — every booking predating the change
 * has a NULL phone, because the form never asked for one. These tests are
 * against the mix it WOULD meet, because "it did nothing on the day" is not
 * evidence that it does the right thing.
 */
class CustomerBackfillTest extends TestCase
{
    use RefreshDatabase;

    private function booking(?string $phone, ?string $name, ?string $email = null, ?string $madeAt = null): Booking
    {
        return Booking::factory()->create([
            'phone' => $phone,
            'name' => $name,
            'email' => $email,
            'created_at' => $madeAt ?? now(),
        ]);
    }

    public function test_one_customer_per_number_however_it_was_typed(): void
    {
        $this->booking('2246698063', 'Omar Haddad', 'omar@example.com', '2026-01-01 10:00:00');
        $this->booking('(224) 669-8063', 'Omar Haddad', 'omar@example.com', '2026-02-01 10:00:00');
        $this->booking('+1 224-669-8063', 'Omar Haddad', 'omar@example.com', '2026-03-01 10:00:00');
        $this->booking('3125550000', 'Eddie Nasser', 'eddie@example.com', '2026-01-15 10:00:00');

        $report = (new CustomerBackfill)->run();

        $this->assertSame(4, $report['bookings']);
        $this->assertSame(4, $report['with_phone']);
        $this->assertSame(2, $report['customers']);
        $this->assertSame(4, $report['linked']);

        $omar = Customer::where('phone', '2246698063')->sole();

        $this->assertSame(3, $omar->bookings_count);
        $this->assertSame('2026-03-01 10:00:00', $omar->last_booked_at->format('Y-m-d H:i:s'));
    }

    public function test_the_name_is_split_on_the_first_space(): void
    {
        $this->booking('2246698001', 'Omar Haddad');
        $this->booking('2246698002', 'Cher');
        $this->booking('2246698003', 'Jean Luc Picard Jr');
        $this->booking('2246698004', '   Spaced   Out   Name  ');

        (new CustomerBackfill)->run();

        $this->assertSame(['Omar', 'Haddad'], $this->nameOf('2246698001'));
        $this->assertSame(['Cher', null], $this->nameOf('2246698002'));
        $this->assertSame(['Jean', 'Luc Picard Jr'], $this->nameOf('2246698003'));
        $this->assertSame(['Spaced', 'Out Name'], $this->nameOf('2246698004'));
    }

    private function nameOf(string $phone): array
    {
        $customer = Customer::where('phone', $phone)->sole();

        return [$customer->first_name, $customer->last_name];
    }

    public function test_bookings_with_no_usable_phone_stay_unlinked(): void
    {
        $none = $this->booking(null, 'No Phone', 'np@example.com');
        $empty = $this->booking('', 'Empty Phone');
        $junk = $this->booking('abc', 'Junk Phone');
        $short = $this->booking('12', 'Short Phone');
        $good = $this->booking('2246698063', 'Omar Haddad');

        $report = (new CustomerBackfill)->run();

        // null and '' never reach the loop at all, so they are not counted
        // as "with a phone". 'abc', '12' and the real number are — and the
        // first two are then rejected. The difference matters when reading
        // the numbers off a deploy log: with_phone is what was looked at,
        // unusable is what was thrown away.
        $this->assertSame(5, $report['bookings']);
        $this->assertSame(3, $report['with_phone']);
        $this->assertSame(2, $report['unusable']);
        $this->assertSame(1, $report['customers']);
        $this->assertSame(1, $report['linked']);

        foreach ([$none, $empty, $junk, $short] as $unlinked) {
            $this->assertNull($unlinked->fresh()->customer_id);
        }

        $this->assertNotNull($good->fresh()->customer_id);
    }

    /** The newest booking is the best guess at who somebody is now. */
    public function test_the_most_recent_booking_supplies_the_details(): void
    {
        $this->booking('2246698063', 'Omar Haddad', 'old@example.com', '2026-01-01 10:00:00');
        $this->booking('2246698063', 'Omar Haddad-Smith', 'new@example.com', '2026-06-01 10:00:00');

        (new CustomerBackfill)->run();

        $customer = Customer::sole();

        $this->assertSame('Haddad-Smith', $customer->last_name);
        $this->assertSame('new@example.com', $customer->email);
    }

    /**
     * Nobody who booked before the consent box existed has agreed to
     * anything. The backfill must never put a number on the SMS list.
     */
    public function test_the_backfill_never_grants_consent(): void
    {
        $this->booking('2246698063', 'Omar Haddad');
        $this->booking('3125550000', 'Eddie Nasser');

        (new CustomerBackfill)->run();

        $this->assertSame(2, Customer::count());
        $this->assertSame(0, Customer::consented()->count());

        foreach (Customer::all() as $customer) {
            $this->assertFalse($customer->sms_marketing_consent);
            $this->assertNull($customer->sms_consent_at);
        }
    }

    public function test_running_it_twice_does_not_duplicate_anybody(): void
    {
        $this->booking('2246698063', 'Omar Haddad');
        $this->booking('2246698063', 'Omar Haddad');

        (new CustomerBackfill)->run();
        $second = (new CustomerBackfill)->run();

        $this->assertSame(1, Customer::count());
        $this->assertSame(1, $second['customers']);
        $this->assertSame(2, Customer::sole()->bookings_count);
    }

    /** Consent already given is not wiped by a second run. */
    public function test_a_second_run_leaves_an_existing_consent_alone(): void
    {
        Customer::factory()->consented()->create(['phone' => '2246698063']);

        $this->booking('2246698063', 'Omar Haddad');

        (new CustomerBackfill)->run();

        $this->assertTrue(Customer::sole()->sms_marketing_consent);
    }

    /**
     * What nexobarbers.com actually looked like on 10 Nov 2026: 1,762
     * bookings, every one of them with a NULL phone.
     */
    public function test_the_shape_of_this_installation_produces_no_customers(): void
    {
        Booking::factory()->count(200)->create(['phone' => null]);

        $report = (new CustomerBackfill)->run();

        $this->assertSame(200, $report['bookings']);
        $this->assertSame(0, $report['with_phone']);
        $this->assertSame(0, $report['customers']);
        $this->assertSame(0, $report['linked']);
        $this->assertSame(0, Customer::count());
        $this->assertSame(0, Booking::whereNotNull('customer_id')->count());
    }
}
