<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingSlotService
{
    public const BOOKING_DURATION_MINUTES = 20;

    public function isTimeInWorkingHours(string $date, string $time): bool
    {
        $dateCarbon = Carbon::parse($date);
        $t = Carbon::parse($time);
        $minutes = $t->hour * 60 + $t->minute;
        $isSunday = $dateCarbon->isSunday();

        if ($isSunday) {
            $startMin = 10 * 60;   // 10:00 AM
            $endMin   = 16 * 60 + 40;  // 4:40 PM
        } else {
            $startMin = 9 * 60;    // 9:00 AM
            $endMin   = 19 * 60 + 40;  // 7:40 PM
        }

        return $minutes >= $startMin && $minutes <= $endMin;
    }

    public function isSlotValidAndAvailable(string $date, string $time): bool
    {
        if (!$this->isTimeInWorkingHours($date, $time)) {
            return false;
        }

        $newStart = Carbon::parse($time)->hour * 60 + Carbon::parse($time)->minute;
        $newEnd   = $newStart + self::BOOKING_DURATION_MINUTES;

        $bookings = Booking::where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($bookings as $b) {
            $bTime  = Carbon::parse($b->time);
            $bStart = $bTime->hour * 60 + $bTime->minute;
            $bEnd   = $bStart + self::BOOKING_DURATION_MINUTES;

            if ($newEnd > $bStart && $newStart < $bEnd) {
                return false;
            }
        }

        return true;
    }

    public function getAvailableSlots(string $date): array
    {
        $dateCarbon = Carbon::parse($date)->startOfDay();
        $isSunday   = $dateCarbon->isSunday();

        $startMin = $isSunday ? 10 * 60 : 9 * 60;
        $endMin   = $isSunday ? 16 * 60 + 40 : 19 * 60 + 40;

        $bookings = Booking::where('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->map(function ($b) {
                $t = Carbon::parse($b->time);
                return $t->hour * 60 + $t->minute;
            })
            ->toArray();

        $now        = Carbon::now();
        $isToday    = $dateCarbon->isSameDay($now);
        $nowMinutes = $now->hour * 60 + $now->minute;

        $slots = [];
        $interval = self::BOOKING_DURATION_MINUTES;

        for ($m = $startMin; $m + $interval <= $endMin + $interval; $m += $interval) {
            if ($m > $endMin) break;

            if ($isToday && $m <= $nowMinutes) {
                continue;
            }

            $newEnd  = $m + $interval;
            $blocked = false;
            foreach ($bookings as $bStart) {
                $bEnd = $bStart + $interval;
                if ($newEnd > $bStart && $m < $bEnd) {
                    $blocked = true;
                    break;
                }
            }
            if ($blocked) continue;

            $hour   = intdiv($m, 60);
            $minute = $m % 60;
            $value  = sprintf('%02d:%02d', $hour, $minute);
            $label  = Carbon::createFromTime($hour, $minute)->format('g:i A');

            $slots[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $slots;
    }
}