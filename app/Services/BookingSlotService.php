<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingSlotService
{
    public const BOOKING_DURATION_MINUTES = 20;

    /**
     * Check if time is within working hours for the date.
     * Sunday: 10:00 - 15:40 (inclusive). Other days: 9:00 - 18:40 (inclusive).
     */
    public function isTimeInWorkingHours(string $date, string $time): bool
    {
        $dateCarbon = Carbon::parse($date);
        $t = Carbon::parse($time);
        $minutes = $t->hour * 60 + $t->minute;
        $isSunday = $dateCarbon->isSunday();
        if ($isSunday) {
            $startMin = 10 * 60 + 0;   // 10:00
            $endMin = 15 * 60 + 40;    // 15:40
        } else {
            $startMin = 9 * 60 + 0;    // 9:00
            $endMin = 18 * 60 + 40;    // 18:40
        }
        return $minutes >= $startMin && $minutes <= $endMin;
    }

    /**
     * Check if time is valid (within hours) and does not overlap with any existing booking.
     */
    public function isSlotValidAndAvailable(string $date, string $time): bool
    {
        $timeNorm = Carbon::parse($time)->format('H:i');
        if (!$this->isTimeInWorkingHours($date, $time)) {
            return false;
        }
        $newStart = Carbon::parse($time)->hour * 60 + Carbon::parse($time)->minute;
        $newEnd = $newStart + self::BOOKING_DURATION_MINUTES;

        $bookings = Booking::where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($bookings as $b) {
            $bTime = Carbon::parse($b->time);
            $bStart = $bTime->hour * 60 + $bTime->minute;
            $bEnd = $bStart + self::BOOKING_DURATION_MINUTES;
            if ($newEnd > $bStart && $newStart < $bEnd) {
                return false;
            }
        }
        return true;
    }
}
