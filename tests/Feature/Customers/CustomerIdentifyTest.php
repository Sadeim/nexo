<?php

namespace Tests\Feature\Customers;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Step one of the booking modal: a phone number, posted on its own.
 *
 * Its job is to keep the number of somebody who may never finish booking, and
 * to tell a returning customer's browser what the shop already knows about
 * them. Both halves have to be exactly right: the first because an abandoned
 * number is the point, the second because the endpoint answers questions
 * about people to anybody who asks.
 */
class CustomerIdentifyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('booking-identify');
    }

    private function identify(array $payload)
    {
        return $this->postJson(route('bookings.identify'), $payload);
    }

    // ---------- the number is kept ----------

    public function test_an_unknown_number_is_saved_immediately(): void
    {
        $this->identify(['phone' => '(224) 669-8063'])
            ->assertOk()
            ->assertJson(['known' => false]);

        // Saved before any booking exists — that is the whole point.
        $customer = Customer::sole();

        $this->assertSame('2246698063', $customer->phone);
        $this->assertNull($customer->first_name);
        $this->assertSame(0, $customer->bookings_count);
    }

    public function test_the_same_person_typing_their_number_differently_is_one_customer(): void
    {
        foreach (['2246698063', '224-669-8063', '+1 (224) 669-8063', '12246698063'] as $typed) {
            $this->identify(['phone' => $typed])->assertOk();
        }

        $this->assertSame(1, Customer::count());
    }

    // ---------- what comes back ----------

    public function test_a_known_number_comes_back_with_what_we_have(): void
    {
        Customer::factory()->create([
            'phone' => '2246698063',
            'first_name' => 'Omar',
            'last_name' => 'Haddad',
            'email' => 'omar@example.com',
        ]);

        $this->identify(['phone' => '224 669 8063'])
            ->assertOk()
            ->assertJson([
                'known' => true,
                'first_name' => 'Omar',
                'last_name' => 'Haddad',
                'email' => 'omar@example.com',
            ]);
    }

    /**
     * "known" is answered from before this request, not after it. The upsert
     * creates the row, so asking afterwards would report every number as one
     * the shop recognises — and prefill a stranger's form with nothing while
     * telling them they were expected.
     */
    public function test_a_first_time_number_is_not_reported_as_known(): void
    {
        $this->identify(['phone' => '2246698063'])
            ->assertOk()
            ->assertJson(['known' => false, 'first_name' => null, 'email' => null]);

        // ...and the second time, it is.
        $this->identify(['phone' => '2246698063'])
            ->assertOk()
            ->assertJson(['known' => true]);
    }

    public function test_nothing_comes_back_for_a_number_that_is_not_one(): void
    {
        foreach (['', 'abcdef', '12', '---', null] as $rubbish) {
            $response = $this->identify(['phone' => $rubbish]);

            $response->assertStatus(422);

            $body = $response->json();

            // Not even "unknown": a refusal must not confirm that a lookup
            // happened at all.
            $this->assertArrayNotHasKey('known', $body);
            $this->assertArrayNotHasKey('first_name', $body);
            $this->assertArrayNotHasKey('email', $body);
        }

        $this->assertSame(0, Customer::count());
    }

    // ---------- consent ----------

    public function test_consent_is_saved_only_when_the_box_is_ticked(): void
    {
        $this->identify(['phone' => '2246698063'])->assertOk();

        $customer = Customer::sole();

        $this->assertFalse($customer->sms_marketing_consent);
        $this->assertNull($customer->sms_consent_at);
        $this->assertNull($customer->consent_source);
    }

    public function test_ticking_the_box_records_when_and_where(): void
    {
        $this->travelTo('2026-11-10 19:30:00');

        $this->identify([
            'phone' => '2246698063',
            'sms_marketing_consent' => 1,
        ])->assertOk();

        $customer = Customer::sole();

        $this->assertTrue($customer->sms_marketing_consent);
        $this->assertSame('2026-11-10 19:30:00', $customer->sms_consent_at->format('Y-m-d H:i:s'));
        $this->assertSame('booking_modal', $customer->consent_source);
    }

    /**
     * An unticked box on a later visit means "this form did not ask me
     * again", not "I take it back". Treating it as a withdrawal would empty
     * the list every time a consenting customer came back to book.
     */
    public function test_a_later_unticked_box_does_not_withdraw_consent(): void
    {
        $this->identify(['phone' => '2246698063', 'sms_marketing_consent' => 1])->assertOk();

        $given = Customer::sole()->sms_consent_at;

        $this->identify(['phone' => '2246698063'])->assertOk();

        $customer = Customer::sole();

        $this->assertTrue($customer->sms_marketing_consent);
        $this->assertEquals($given, $customer->sms_consent_at, 'The original consent timestamp moved.');
    }

    public function test_consent_is_never_granted_by_the_phone_field_alone(): void
    {
        // Every falsy shape a checkbox can arrive as.
        foreach ([0, '0', false, 'false', null, ''] as $unticked) {
            Customer::query()->delete();

            $this->identify(['phone' => '2246698063', 'sms_marketing_consent' => $unticked])->assertOk();

            $this->assertFalse(
                Customer::sole()->sms_marketing_consent,
                var_export($unticked, true).' was treated as consent.'
            );
        }
    }

    // ---------- throttling ----------

    public function test_the_lookup_is_throttled(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->identify(['phone' => '22466980'.str_pad((string) $i, 2, '0', STR_PAD_LEFT)])
                ->assertOk();
        }

        $this->identify(['phone' => '2246698099'])->assertStatus(429);
    }

    /**
     * A malformed number still spends an attempt. Otherwise the throttle
     * could be sidestepped by interleaving junk, and — worse — the rejection
     * would be a free oracle for which shapes the endpoint even looks at.
     */
    public function test_refused_numbers_count_against_the_allowance(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->identify(['phone' => 'nonsense'])->assertStatus(422);
        }

        $this->identify(['phone' => '2246698063'])->assertStatus(429);
    }
}
