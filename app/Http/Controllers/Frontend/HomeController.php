<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Mail\AdminNotification;
use App\Mail\AdminBookingNotification;
use App\Mail\BookingConfirmationMail;
use App\Models\About;
use App\Models\Achievement;
use App\Models\Blog;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Client;
use App\Models\Consultation;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\How;
use App\Models\Instagram;
use App\Models\Newsletter;

use App\Models\Section;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slider;

use App\Models\UserMessages;
use App\Models\Work;
use App\Services\BookingSlotService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Jobs\SendBookingReminderJob;


class HomeController extends Controller
{
    public function home(Request $request)
    {
        // Barbers published for the website, in the owner's chosen order.
        $data['barbers'] = Employee::publicBarbers()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // ?barber=slug arrives from a "Book with X" button. Resolved to an id
        // here so the form only ever deals in ids and a slug can never reach
        // the validator or the database.
        $data['preselectedBarber'] = $request->filled('barber')
            ? $data['barbers']->firstWhere('slug', $request->query('barber'))
            : null;

        $data['slider'] = Slider::first();
        $data['about'] = About::first();
        $data['works'] = Work::where('is_featured', 1)->get();
        $data['services'] = Service::where('is_featured', 0)->get();
        $data['services_packages'] = Service::where('is_featured', 1)->get();


        $data['sections'] = Section::whereIn('key', [
            'services_section',
            'contact_page',
        ])->get()->keyBy('key');

        return view('frontend.home', $data);
    }

    public function services()
    {
        $data['services'] = Service::get();

        $data['works'] = Work::where('is_featured', 1)->get();

        $data['sections'] = Section::whereIn('key', [
            'services_section',
        ])->get()->keyBy('key');

        return view('frontend.services', $data);
    }

    public function gallery()
    {
        $data['works'] = Work::where('is_featured', 0)->get();

        return view('frontend.gallery', $data);
    }

    // Get all active services for the booking form
    public function getServices()
    {
        $services = Service::get(['id', 'name']);
        return response()->json(['services' => $services]);
    }

    // Store booking
    // public function storeBooking(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email',
    //         'date' => 'required|date|after_or_equal:today',
    //         'service_id' => 'required|exists:services,id',
    //         'time' => 'required|string',
    //     ]);

    //     $slotService = app(BookingSlotService::class);
    //     if (!$slotService->isSlotValidAndAvailable($validated['date'], $validated['time'])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'The selected time is not available. Please choose another slot.',
    //         ], 422);
    //     }

    //     $validated['persons'] = 1;
    //     $validated['status'] = 'pending';
    //     $validated['time'] = Carbon::parse($validated['time'])->format('H:i:s');

    //     $booking = Booking::create($validated);
    //     $booking->load('service');

    //     try {
    //         Mail::to($validated['email'])->send(new BookingConfirmationMail($booking));
    //         Mail::to(config('mail.admin_email'))->send(new AdminBookingNotification($booking));
    //     } catch (\Throwable $e) {
    //         \Log::warning('Booking emails failed: ' . $e->getMessage());
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Booking saved successfully. A confirmation email has been sent to your email.',
    //     ], 200);
    // }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email',
            'date'       => 'required|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
            'time'       => 'required|string',
            // Absent or null means "any available barber". Only a published
            // barber may be requested — an unpublished employee is not on
            // offer, and accepting one would book a chair the customer was
            // never shown.
            'barber_id'  => ['nullable', Rule::exists('employees', 'id')->where(
                fn ($q) => $q->where('public_profile', true)->where('is_active', true)
            )],
        ]);

        $slotService = app(BookingSlotService::class);

        $barberId = isset($validated['barber_id']) ? (int) $validated['barber_id'] : null;

        $validated['barber_id'] = $barberId;
        $validated['persons']   = 1;
        $validated['status']    = 'pending';
        $validated['time']      = Carbon::parse($validated['time'])->format('H:i:s');

        try {
            // Everything touching this slot is serialised by a named lock, so
            // two customers racing for the same time take their turn rather
            // than both passing the capacity count. Inside it, one transaction
            // covers both halves of the rule: the re-check runs with
            // $forUpdate, and the insert goes through guardSlotUniqueness so
            // the per-barber constraint reads back as the same refusal.
            $booking = $slotService->withSlotLock(
                $validated['date'],
                $validated['time'],
                fn () => DB::transaction(function () use ($slotService, $validated, $barberId) {
                    $available = $slotService->isSlotValidAndAvailable(
                        $validated['date'],
                        $validated['time'],
                        $barberId,
                        forUpdate: true
                    );

                    if (!$available) {
                        throw $barberId === null
                            ? SlotUnavailableException::forShop()
                            : SlotUnavailableException::forBarber();
                    }

                    return $slotService->guardSlotUniqueness(
                        fn () => Booking::create($validated)
                    );
                })
            );
        } catch (SlotUnavailableException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $booking->load(['service', 'barber']);

        // إرسال إيميل التأكيد
        try {
            Mail::to($validated['email'])->send(new BookingConfirmationMail($booking));
            Mail::to(config('mail.admin_email'))->send(new AdminBookingNotification($booking));
        } catch (\Throwable $e) {
            \Log::warning('Booking emails failed: ' . $e->getMessage());
        }

        // جدولة إشعار التذكير قبل الموعد بـ 30 دقيقة
        $appointmentDateTime = Carbon::parse(
            $booking->date->format('Y-m-d') . ' ' . $booking->time
        );
        $reminderTime = $appointmentDateTime->subMinutes(30);

        // أرسل التذكير فقط إذا كان وقت التذكير في المستقبل
        if ($reminderTime->isFuture()) {
            SendBookingReminderJob::dispatch($booking)
                ->delay($reminderTime);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking saved successfully. A confirmation email has been sent to your email.',
        ], 200);
    }

    // Get available time slots for a specific date
    public function getAvailableSlots(Request $request)
    {
        $validated = $request->validate([
            'date'      => 'nullable|date',
            // Narrows availability to one barber. Omitted means "any".
            'barber_id' => ['nullable', Rule::exists('employees', 'id')->where(
                fn ($q) => $q->where('public_profile', true)->where('is_active', true)
            )],
        ]);

        if (empty($validated['date'])) {
            return response()->json(['success' => true, 'slots' => []], 200);
        }

        $slots = app(BookingSlotService::class)->getAvailableSlots(
            $validated['date'],
            isset($validated['barber_id']) ? (int) $validated['barber_id'] : null
        );

        // Each slot carries `disabled` so the UI can grey out a taken time
        // rather than hiding it, which reads as the shop being shut.
        return response()->json([
            'success' => true,
            'slots'   => $slots,
        ], 200);
    }

    public function contactUs()
    {
        $data['sections'] = Section::whereIn('key', [
            'contact_page',

        ])->get()->keyBy('key');


        return view('frontend.contact', $data);
    }


    public function contactStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $dataR = $request->only(['name', 'email', 'subject', 'message']);
        $message = UserMessages::create($dataR);
        $email = Setting::where('key', 'email')->first()->value;
        Mail::to($email)->send(new AdminNotification($message, 'contact'));

        return response()->json([
            'success' => true,
            'message' => 'Done Successfully'
        ], 200);
    }
}
