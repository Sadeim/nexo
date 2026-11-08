<?php

namespace App\Observers;

use App\Models\Booking;

/**
 * Keeps `bookings.slot_key` in step with the row it describes.
 *
 * The key backs a unique index, so it is only as trustworthy as its weakest
 * writer — one controller setting it by hand in a slightly different format
 * and the constraint silently stops catching anything. So exactly one place
 * computes it, on every save, and no caller is expected to remember.
 */
class BookingObserver
{
    /**
     * Runs for both inserts and updates, which is the point: a booking that
     * is cancelled, reassigned to another barber, or moved to another time
     * has to release or claim a slot, and all three arrive as updates.
     */
    public function saving(Booking $booking): void
    {
        $booking->slot_key = $booking->resolveSlotKey();
    }
}
