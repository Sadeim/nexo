<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Employee;
use Carbon\Carbon;

/**
 * The single authority on when the shop can take a booking.
 *
 * Everything that decides whether a slot is bookable goes through here: the
 * public slot list, the server-side guard in storeBooking, and the admin's
 * barber-assignment dropdown. Opening hours and the appointment length live
 * here as constants so no caller invents a second copy of them.
 *
 * CAPACITY
 * The shop can serve as many customers at once as it has published barbers,
 * so one slot holds `capacity()` bookings rather than one. With no published
 * barbers the capacity is 1, which is how the shop behaved before barbers
 * existed and keeps the site bookable while profiles are being set up.
 *
 * A booking either names a barber or means "any available barber" (NULL).
 * That gives two questions, and a slot must pass both:
 *   - is the shop full at this time?            total occupants < capacity
 *   - is THIS barber already busy at this time? only when one was named
 *
 * OCCUPANCY
 * "Occupied" means any booking whose status is not `cancelled` — matching
 * SendBookingReminders. The two methods in this class previously disagreed
 * about this (one excluded only `cancelled`, the other counted only `pending`
 * and `confirmed`), so a booking in any other state blocked the server guard
 * while staying invisible in the slot list.
 */
class BookingSlotService
{
    /** How long one appointment occupies a barber. */
    public const BOOKING_DURATION_MINUTES = 20;

    /** Opening hours as minutes from midnight. Last values are SLOT STARTS. */
    public const WEEKDAY_OPEN_MINUTE      = 9 * 60;        //  9:00 AM
    public const WEEKDAY_LAST_SLOT_MINUTE = 19 * 60 + 40;  //  7:40 PM start
    public const SUNDAY_OPEN_MINUTE       = 10 * 60;       // 10:00 AM
    public const SUNDAY_LAST_SLOT_MINUTE  = 16 * 60 + 40;  //  4:40 PM start

    /** A booking in this state frees its slot again. */
    public const STATUS_CANCELLED = 'cancelled';

    // ---------- Opening hours ----------

    /**
     * Opening minute and last bookable slot start for a given date.
     *
     * @return array{0:int,1:int}
     */
    public function workingWindow(string $date): array
    {
        return Carbon::parse($date)->isSunday()
            ? [self::SUNDAY_OPEN_MINUTE, self::SUNDAY_LAST_SLOT_MINUTE]
            : [self::WEEKDAY_OPEN_MINUTE, self::WEEKDAY_LAST_SLOT_MINUTE];
    }

    /**
     * Display-ready opening hours, so a page showing them reads these
     * constants instead of restating them.
     *
     * The closing time shown is when the last appointment ENDS, which is what
     * a customer means by "closes at", not the last slot start.
     *
     * @return array<int,array{days:string,open:string,close:string}>
     */
    public function shopHours(): array
    {
        $fmt = fn (int $minutes): string => Carbon::createFromTime(
            intdiv($minutes, 60),
            $minutes % 60
        )->format('g:i A');

        return [
            [
                'days'  => 'Monday – Saturday',
                'open'  => $fmt(self::WEEKDAY_OPEN_MINUTE),
                'close' => $fmt(self::WEEKDAY_LAST_SLOT_MINUTE + self::BOOKING_DURATION_MINUTES),
            ],
            [
                'days'  => 'Sunday',
                'open'  => $fmt(self::SUNDAY_OPEN_MINUTE),
                'close' => $fmt(self::SUNDAY_LAST_SLOT_MINUTE + self::BOOKING_DURATION_MINUTES),
            ],
        ];
    }

    public function isTimeInWorkingHours(string $date, string $time): bool
    {
        [$openMin, $lastSlotMin] = $this->workingWindow($date);
        $minutes = $this->toMinutes($time);

        return $minutes >= $openMin && $minutes <= $lastSlotMin;
    }

    // ---------- Capacity ----------

    /**
     * How many customers the shop can serve in one slot.
     *
     * Falls back to 1 when nobody is published, so the site stays bookable
     * while profiles are being filled in rather than silently refusing every
     * slot.
     */
    public function capacity(): int
    {
        return max(1, Employee::publicBarbers()->count());
    }

    // ---------- Availability ----------

    /**
     * Can this exact date/time still be booked, optionally for one barber?
     *
     * Pass $forUpdate inside a DB transaction to hold the slot's rows while
     * the booking is written — see storeBooking. On its own it is a read and
     * the answer can go stale a moment later.
     */
    public function isSlotValidAndAvailable(
        string $date,
        string $time,
        ?int $barberId = null,
        bool $forUpdate = false
    ): bool {
        if (!$this->isTimeInWorkingHours($date, $time)) {
            return false;
        }

        $slotStart = $this->toMinutes($time);

        // A slot that has already started is gone. The slot list has always
        // hidden these; the server guard used to accept them, so a stale page
        // could book a time that had passed.
        if ($this->hasStarted($date, $slotStart)) {
            return false;
        }

        return $this->slotIsOpen(
            $this->occupantsOfDay($date, $forUpdate),
            $slotStart,
            $barberId,
            $this->capacity()
        );
    }

    /**
     * Every bookable slot for a date, each flagged with whether it is taken.
     *
     * Taken slots are returned `disabled` rather than omitted so the UI can
     * grey them out — a customer seeing "4:20 PM, unavailable" understands the
     * shop is busy, where a missing row just looks like the shop is shut.
     * Slots whose time has already passed are still omitted: they are gone,
     * not busy.
     *
     * @return array<int,array{value:string,label:string,disabled:bool}>
     */
    public function getAvailableSlots(string $date, ?int $barberId = null): array
    {
        [$openMin, $lastSlotMin] = $this->workingWindow($date);

        $occupants = $this->occupantsOfDay($date);
        $capacity  = $this->capacity();

        $slots = [];

        for ($m = $openMin; $m <= $lastSlotMin; $m += self::BOOKING_DURATION_MINUTES) {
            if ($this->hasStarted($date, $m)) {
                continue;
            }

            $slots[] = [
                'value'    => sprintf('%02d:%02d', intdiv($m, 60), $m % 60),
                'label'    => Carbon::createFromTime(intdiv($m, 60), $m % 60)->format('g:i A'),
                'disabled' => !$this->slotIsOpen($occupants, $m, $barberId, $capacity),
            ];
        }

        return $slots;
    }

    // ---------- Internals ----------

    /**
     * The one rule, shared by the slot list and the server guard so they can
     * never drift apart.
     *
     * @param  array<int,array{start:int,barber_id:?int}>  $occupants
     */
    private function slotIsOpen(array $occupants, int $slotStart, ?int $barberId, int $capacity): bool
    {
        $taken      = 0;
        $barberBusy = false;

        foreach ($occupants as $occupant) {
            if (!$this->overlapsSlot($occupant['start'], $slotStart)) {
                continue;
            }

            $taken++;

            if ($barberId !== null
                && $occupant['barber_id'] !== null
                && (int) $occupant['barber_id'] === $barberId) {
                $barberBusy = true;
            }
        }

        if ($taken >= $capacity) {
            return false;
        }

        return !$barberBusy;
    }

    /**
     * Non-cancelled bookings for a date, reduced to what the rule needs.
     *
     * @return array<int,array{start:int,barber_id:?int}>
     */
    private function occupantsOfDay(string $date, bool $forUpdate = false): array
    {
        // Matched as a half-open day range rather than by equality.
        //
        // Booking::$casts declares `date` as a date, and Laravel writes it
        // through the connection's datetime format — so the stored value is
        // "2026-11-11 00:00:00". MySQL's DATE column silently truncates that,
        // which is the only reason `where('date', $date)` ever matched; on any
        // driver that keeps what it was given it matches nothing at all and
        // every slot reads as free.
        //
        // A range works on both, and unlike whereDate() it leaves the
        // (date, time) index usable.
        $dayStart = Carbon::parse($date)->startOfDay();

        $query = Booking::query()
            ->where('date', '>=', $dayStart->toDateString())
            ->where('date', '<', $dayStart->copy()->addDay()->toDateString())
            ->where('status', '!=', self::STATUS_CANCELLED);

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->get(['id', 'time', 'barber_id'])
            ->map(fn (Booking $booking) => [
                'start'     => $this->toMinutes((string) $booking->time),
                'barber_id' => $booking->barber_id,
            ])
            ->all();
    }

    /**
     * Do two appointments of equal length collide?
     *
     * Compared as ranges rather than equal start times because a booking
     * created by hand in the dashboard need not sit on the 20-minute grid,
     * and such a booking still ties up a barber across the slots it spans.
     */
    private function overlapsSlot(int $bookingStart, int $slotStart): bool
    {
        $length = self::BOOKING_DURATION_MINUTES;

        return $bookingStart < $slotStart + $length
            && $bookingStart + $length > $slotStart;
    }

    /** Is this slot in the past? Only ever true for today. */
    private function hasStarted(string $date, int $slotStart): bool
    {
        $now = Carbon::now();

        if (!Carbon::parse($date)->startOfDay()->isSameDay($now)) {
            return false;
        }

        return $slotStart <= $now->hour * 60 + $now->minute;
    }

    private function toMinutes(string $time): int
    {
        $parsed = Carbon::parse($time);

        return $parsed->hour * 60 + $parsed->minute;
    }
}
