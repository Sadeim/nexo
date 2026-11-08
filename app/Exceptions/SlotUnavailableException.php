<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * The requested date/time can no longer be booked.
 *
 * Thrown both when BookingSlotService's rule says so up front and when the
 * database's per-barber uniqueness constraint says so a moment later, so
 * callers answer a customer the same way whichever of the two caught it.
 */
class SlotUnavailableException extends RuntimeException
{
    public static function forBarber(): self
    {
        return new self('That barber is already booked at this time. Please choose another slot.');
    }

    public static function forShop(): self
    {
        return new self('The selected time is not available. Please choose another slot.');
    }
}
