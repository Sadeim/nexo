<?php

namespace Tests\Feature\Customers;

use App\Models\Employee;
use App\Support\Phone;
use Tests\TestCase;

/**
 * One phone number, however it is typed.
 *
 * Two parts of this system identify people by phone — barbers signing in to
 * the portal, customers booking a chair — and both now come through
 * App\Support\Phone. If these two disagreed, a returning customer would
 * quietly become a second customer and drop out of their own history.
 */
class PhoneNormalizationTest extends TestCase
{
    public static function sameNumber(): array
    {
        return [
            'bare' => ['2246698063'],
            'dashes' => ['224-669-8063'],
            'brackets' => ['(224) 669-8063'],
            'spaces' => ['224 669 8063'],
            'dots' => ['224.669.8063'],
            'country code' => ['12246698063'],
            'plus one' => ['+1 (224) 669-8063'],
            'plus one dashes' => ['+1-224-669-8063'],
            'trailing space' => ['  2246698063  '],
        ];
    }

    /** @dataProvider sameNumber */
    public function test_every_way_of_writing_one_number_lands_on_the_same_digits(string $typed): void
    {
        $this->assertSame('2246698063', Phone::normalize($typed));
    }

    public function test_the_portal_and_the_booking_form_normalise_identically(): void
    {
        foreach (self::sameNumber() as [$typed]) {
            $this->assertSame(
                Phone::normalize($typed),
                Employee::normalizePhone($typed),
                'The barber portal and the booking form disagree about '.$typed
            );
        }
    }

    public function test_nothing_is_not_a_number(): void
    {
        foreach ([null, '', '   ', 'abc', '---'] as $nothing) {
            $this->assertNull(Phone::normalize($nothing));
            $this->assertFalse(Phone::isPlausible($nothing));
        }
    }

    /**
     * The country-code rule fires on eleven digits beginning with 1, and
     * nowhere else. A ten-digit number that happens to start with 1 keeps all
     * ten.
     */
    public function test_only_an_eleven_digit_leading_one_loses_a_digit(): void
    {
        $this->assertSame('2246698063', Phone::normalize('12246698063'));
        $this->assertSame('1224669806', Phone::normalize('1224669806'));
        $this->assertSame('22466980631', Phone::normalize('22466980631'));
        $this->assertSame('9876543210', Phone::normalize('9876543210'));
    }

    public function test_a_number_too_short_to_dial_is_refused(): void
    {
        $this->assertFalse(Phone::isPlausible('12345'));
        $this->assertTrue(Phone::isPlausible('2246698'));
        $this->assertTrue(Phone::isPlausible('2246698063'));
        // Longer than E.164 allows.
        $this->assertFalse(Phone::isPlausible('1234567890123456'));
    }

    public function test_display_and_messaging_formats(): void
    {
        $this->assertSame('(224) 669-8063', Phone::pretty('+1 224 669 8063'));
        $this->assertSame('+12246698063', Phone::e164('(224) 669-8063'));

        // A number that is not ten digits is not assumed to be American.
        $this->assertSame('+4407911123456', Phone::e164('4407911123456'));
        $this->assertNull(Phone::e164('nonsense'));
        $this->assertSame('', Phone::pretty(null));
    }
}
