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
use App\Services\CustomerService;
use App\Support\Phone;

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
            // Split into two fields, because a customer record needs to know
            // which part of "Omar Haddad" to put in a text message.
            'first_name' => 'required|string|max:120',
            'last_name'  => 'nullable|string|max:120',

            /*
             * The phone is now the required one and the email is not.
             *
             * That is the whole point of the change: a phone number reaches
             * this shop's customers and an email address mostly does not. A
             * booking with no email simply gets no confirmation email, which
             * is handled below rather than refused here.
             */
            'phone'      => ['required', 'string', 'max:32'],
            'email'      => 'nullable|email',
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

        if (! Phone::isPlausible($validated['phone'])) {
            return response()->json([
                'success' => false,
                'errors' => ['phone' => ['Enter a phone number we can reach you on.']],
            ], 422);
        }

        $slotService = app(BookingSlotService::class);

        $barberId = isset($validated['barber_id']) ? (int) $validated['barber_id'] : null;

        /*
         * The booking keeps the name as one string, as it always has. It is
         * the record of what was typed on the day; the customer row is the
         * thing that gets corrected later.
         */
        $fullName = trim($validated['first_name'].' '.($validated['last_name'] ?? ''));

        $attributes = [
            'name'       => $fullName,
            'email'      => $validated['email'] ?? null,
            'phone'      => Phone::normalize($validated['phone']),
            'date'       => $validated['date'],
            'time'       => Carbon::parse($validated['time'])->format('H:i:s'),
            'service_id' => $validated['service_id'],
            'barber_id'  => $barberId,
            'persons'    => 1,
            'status'     => 'pending',
        ];

        try {
            // Everything touching this slot is serialised by a named lock, so
            // two customers racing for the same time take their turn rather
            // than both passing the capacity count. Inside it, one transaction
            // covers both halves of the rule: the re-check runs with
            // $forUpdate, and the insert goes through guardSlotUniqueness so
            // the per-barber constraint reads back as the same refusal.
            $booking = $slotService->withSlotLock(
                $attributes['date'],
                $attributes['time'],
                fn () => DB::transaction(function () use ($slotService, $attributes, $barberId) {
                    $available = $slotService->isSlotValidAndAvailable(
                        $attributes['date'],
                        $attributes['time'],
                        $barberId,
                        forUpdate: true
                    );

                    if (!$available) {
                        throw $barberId === null
                            ? SlotUnavailableException::forShop()
                            : SlotUnavailableException::forBarber();
                    }

                    return $slotService->guardSlotUniqueness(
                        fn () => Booking::create($attributes)
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

        /*
         * The customer behind the booking.
         *
         * Step one of the modal has almost certainly created this row
         * already — upsertByPhone finds it and folds in the name and email
         * that only step two knows. Done after the booking exists, so a slot
         * that turns out to be taken does not leave a customer record behind
         * for an appointment that was never made.
         */
        $customer = app(CustomerService::class)->upsertByPhone($validated['phone'], [
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'] ?? null,
            'email'      => $validated['email'] ?? null,
        ]);

        if ($customer) {
            app(CustomerService::class)->attachBooking($customer, $booking);
        }

        /*
         * Two emails, two try blocks.
         *
         * They used to share one, which meant a failure sending to the
         * customer took the shop's own notification down with it — the one
         * message that must arrive, lost to the one that is optional. Now the
         * customer's is skipped entirely when there is no address, and
         * neither can prevent the other.
         */
        if (filled($booking->email)) {
            try {
                Mail::to($booking->email)->send(new BookingConfirmationMail($booking));
            } catch (\Throwable $e) {
                \Log::warning('Booking confirmation email failed: '.$e->getMessage());
            }
        }

        try {
            Mail::to(config('mail.admin_email'))->send(new AdminBookingNotification($booking));
        } catch (\Throwable $e) {
            \Log::warning('Booking admin notification failed: '.$e->getMessage());
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
            // Only promise an email when one is actually going out.
            'message' => filled($booking->email)
                ? 'Booking saved. A confirmation email is on its way.'
                : 'Booking saved. We have your number if anything changes.',
        ], 200);
    }

    /**
     * Step one of the booking modal: a phone number, and nothing else.
     *
     * Writes the customer immediately, before anything is booked, because the
     * number is worth keeping even from somebody who gets as far as typing it
     * and then closes the tab. If they come back, the rest of the form is
     * already filled in.
     *
     * Throttled to ten a minute per IP. Note what this endpoint is: an
     * unauthenticated lookup that answers "is this number one of yours, and
     * what is their name" — the throttle is the only thing between it and
     * somebody walking the dialling plan, and it is worth revisiting before
     * the list is large enough to be worth harvesting.
     */
    public function identifyCustomer(Request $request)
    {
        $phone = $request->input('phone');

        /*
         * Nothing at all comes back for a number that is not a number. Not
         * "unknown", which would still confirm the endpoint had looked.
         */
        if (! Phone::isPlausible($phone)) {
            return response()->json([
                'success' => false,
                'errors' => ['phone' => ['Enter a phone number we can reach you on.']],
            ], 422);
        }

        $service = app(CustomerService::class);

        // Was this number already known BEFORE this request created anything?
        // Asked first, because the upsert below is about to make the answer
        // yes either way.
        $known = \App\Models\Customer::where('phone', Phone::normalize($phone))->first();

        $customer = $service->upsertByPhone($phone, [
            'sms_marketing_consent' => $request->boolean('sms_marketing_consent'),
            'consent_source' => 'booking_modal',
        ]);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'errors' => ['phone' => ['Enter a phone number we can reach you on.']],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'known' => (bool) $known,
            'first_name' => $known?->first_name,
            'last_name' => $known?->last_name,
            'email' => $known?->email,
        ]);
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
