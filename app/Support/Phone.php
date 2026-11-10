<?php

namespace App\Support;

/**
 * Phone numbers, reduced to one shape.
 *
 * Two parts of this system identify a person by their phone — a barber
 * signing in to the portal, and a customer booking a chair — and they have to
 * agree on what "the same number" means. The owner types what is in their
 * contacts, the customer types what is on their phone, and months later
 * somebody types it a third way; all three have to land on one value or the
 * lookup silently fails and a duplicate is created instead.
 *
 * This was Employee::normalizePhone until customers needed it too. That model
 * now delegates here, so there is one definition rather than two that drift.
 */
class Phone
{
    /**
     * Digits only, with a US country code dropped.
     *
     * Eleven digits beginning with 1 is "+1" in front of a ten-digit number,
     * so 12246698063 and 2246698063 are the same phone. The length check is
     * what keeps that safe — it fires only on 11 digits, so a genuine
     * ten-digit number starting with 1 (which the NANP does not issue as an
     * area code anyway) is untouched, and so is any other country's number of
     * a different length.
     *
     * Returns null rather than an empty string, so "no number" is one value
     * and not two.
     */
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            $digits = substr($digits, 1);
        }

        return $digits === '' ? null : $digits;
    }

    /**
     * Whether a number is worth storing.
     *
     * Seven digits is the shortest thing anyone could call; fifteen is the
     * E.164 maximum. Everything in between is accepted because this shop has
     * no business deciding that a customer's number is the wrong shape.
     */
    public static function isPlausible(?string $phone): bool
    {
        $digits = self::normalize($phone);

        return $digits !== null && strlen($digits) >= 7 && strlen($digits) <= 15;
    }

    /** (224) 669-8063 for a US number; the digits unchanged for anything else. */
    public static function pretty(?string $phone): string
    {
        $digits = self::normalize($phone);

        if ($digits === null) {
            return '';
        }

        if (strlen($digits) === 10) {
            return sprintf('(%s) %s-%s',
                substr($digits, 0, 3), substr($digits, 3, 3), substr($digits, 6));
        }

        return $digits;
    }

    /**
     * The form a messaging provider wants.
     *
     * Ten digits are assumed to be US, which is the only country this shop
     * takes bookings from. Anything else is passed through with a +, because
     * guessing a country code for a number we do not understand would be
     * worse than handing the provider something it can reject.
     */
    public static function e164(?string $phone): ?string
    {
        $digits = self::normalize($phone);

        if ($digits === null) {
            return null;
        }

        return strlen($digits) === 10 ? '+1'.$digits : '+'.$digits;
    }
}
