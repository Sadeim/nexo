<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Models\Booking;
use App\Services\BookingSlotService;
use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_bookings|add_bookings', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_bookings', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_bookings', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_bookings', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.bookings.index');
    }

    public function datatable(Request $request)
    {
        $items = Booking::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        // return view('admin.bookings.create');
    }

    public function store(CreateBookingRequest $request) {}

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.create', compact('booking'));
    }

  

    public function update(CreateBookingRequest $request, $id) {}

    /**
     * Assign (or clear) the barber on an existing booking.
     *
     * Goes through the same guarded path as a customer booking: the write sets
     * barber_id, the observer recomputes slot_key, and the unique index
     * refuses a barber who is already taken at that time.
     *
     * Capacity is deliberately NOT re-checked. This booking already occupies a
     * chair — counting it against the limit would read as "full" and block
     * every reassignment. Only the per-barber half can be violated here, and
     * that half is the one the database enforces.
     */
    public function assignBarber(Request $request, $id)
    {
        $data = $request->validate([
            'barber_id' => ['nullable', Rule::exists('employees', 'id')->where(
                fn ($q) => $q->where('public_profile', true)->where('is_active', true)
            )],
        ]);

        $booking  = Booking::findOrFail($id);
        $barberId = ($data['barber_id'] ?? null) !== null ? (int) $data['barber_id'] : null;
        $slots    = app(BookingSlotService::class);

        try {
            // Same named lock as a customer booking, keyed on this booking's
            // own slot — so assigning from the dashboard queues behind a
            // customer booking the same time rather than racing it.
            $slots->withSlotLock(
                $booking->date->toDateString(),
                (string) $booking->time,
                fn () => DB::transaction(function () use ($slots, $booking, $barberId) {
                    $slots->guardSlotUniqueness(
                        fn () => $booking->update(['barber_id' => $barberId])
                    );
                })
            );
        } catch (SlotUnavailableException $e) {
            // The row is untouched: the collision rolled the transaction back.
            return $this->response_api(422, 'Barber already booked at this time');
        }

        return $this->response_api(200, $barberId === null
            ? 'Booking set to any available barber'
            : 'Barber assigned');
    }

    public function destroy($id)
    {
        Booking::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }

    public function calendar()
    {
        return view('admin.bookings.calendar');
    }

    public function calendarEvents(Request $request)
    {
        $bookings = Booking::with('service')->get();

        $events = $bookings->map(function ($booking) {
            // دمج التاريخ مع الوقت
            $start = $booking->date->format('Y-m-d');
            if ($booking->time) {
                $start .= 'T' . $booking->time;
            }

            return [
                'id'    => $booking->id,
                'title' => ($booking->service ? $booking->service->name : 'No Service') . ' - ' . $booking->name,
                'start' => $start,
                'extendedProps' => [
                    'name'    => $booking->name,
                    'email'   => $booking->email,
                    'phone'   => $booking->phone,
                    'persons' => $booking->persons,
                    'service' => $booking->service ? $booking->service->name : '-',
                    'time'    => $booking->time,
                    'message' => $booking->message,
                    'status'  => $booking->status,
                ],
            ];
        });

        return response()->json($events);
    }
}
