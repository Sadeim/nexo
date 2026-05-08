<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingSlotService
{
    public const WORK_START_MIN = 9 * 60;   // 9:00 AM
    public const WORK_END_MIN = 18 * 60;    // 6:00 PM (last appointment must end by this)

    /**
     * Returns the slot/booking interval (minutes) for the given date.
     * Wed–Sat: 10 minutes. Sun, Mon, Tue: 20 minutes.
     */
    public function getDayInterval(Carbon $date): int
    {
        $dow = $date->dayOfWeek; // Sun=0, Mon=1, Tue=2, Wed=3, Thu=4, Fri=5, Sat=6
        return ($dow >= 3 && $dow <= 6) ? 10 : 20;
    }

    /**
     * Check if a time is aligned with the day's slot grid and within working hours.
     */
    public function isTimeInWorkingHours(string $date, string $time): bool
    {
        $dateCarbon = Carbon::parse($date);
        $t = Carbon::parse($time);
        $minutes = $t->hour * 60 + $t->minute;
        $interval = $this->getDayInterval($dateCarbon);

        if ($minutes < self::WORK_START_MIN) return false;
        if ($minutes + $interval > self::WORK_END_MIN) return false;
        if (($minutes - self::WORK_START_MIN) % $interval !== 0) return false;

        return true;
    }

    /**
     * Check if time is within hours and does not overlap with any existing booking.
     */
    public function isSlotValidAndAvailable(string $date, string $time): bool
    {
        if (!$this->isTimeInWorkingHours($date, $time)) {
            return false;
        }

        $dateCarbon = Carbon::parse($date);
        $interval = $this->getDayInterval($dateCarbon);

        $newStart = Carbon::parse($time)->hour * 60 + Carbon::parse($time)->minute;
        $newEnd = $newStart + $interval;

        $bookings = Booking::where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($bookings as $b) {
            $bTime = Carbon::parse($b->time);
            $bStart = $bTime->hour * 60 + $bTime->minute;
            $bEnd = $bStart + $interval;
            if ($newEnd > $bStart && $newStart < $bEnd) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build all available time slots for a given date, excluding booked ones
     * and past slots if date is today.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function getAvailableSlots(string $date): array
    {
        $dateCarbon = Carbon::parse($date)->startOfDay();
        $interval = $this->getDayInterval($dateCarbon);

        $bookings = Booking::where('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->map(function ($b) {
                $t = Carbon::parse($b->time);
                return $t->hour * 60 + $t->minute;
            })
            ->toArray();

        $now = Carbon::now();
        $isToday = $dateCarbon->isSameDay($now);
        $nowMinutes = $now->hour * 60 + $now->minute;

        $slots = [];
        for ($m = self::WORK_START_MIN; $m + $interval <= self::WORK_END_MIN; $m += $interval) {
            // Skip past slots if booking for today
            if ($isToday && $m <= $nowMinutes) {
                continue;
            }

            // Skip if overlaps with an existing booking
            $newEnd = $m + $interval;
            $blocked = false;
            foreach ($bookings as $bStart) {
                $bEnd = $bStart + $interval;
                if ($newEnd > $bStart && $m < $bEnd) {
                    $blocked = true;
                    break;
                }
            }
            if ($blocked) continue;

            $hour = intdiv($m, 60);
            $minute = $m % 60;
            $value = sprintf('%02d:%02d', $hour, $minute);
            $label = Carbon::createFromTime($hour, $minute)->format('g:i A');

            $slots[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $slots;
    }
}
