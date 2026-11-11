{{--
    The booking modal.

    Extracted from footer.blade.php, which had grown to 969 lines because this
    lived inside it — and the footer renders on every page.

    Field order is barber, date, time: each one narrows the next, so asking in
    that order means never having to go back. The barber select is populated
    from $shared_barbers (see AppServiceProvider) rather than a controller, so
    the modal works wherever the footer renders.
--}}
@php
    $modalBarbers = $shared_barbers ?? collect();
    // Set by HomeController from ?barber=slug, so the slug never reaches the
    // form or the validator — only an id does.
    $lockedBarber = $preselectedBarber ?? null;
@endphp

 <!-- Booking Modal -->
 {{-- The Google Ads conversion target, for booking.js. Both halves are
      needed before anything is reported: with no label configured the
      attribute is empty and the script sends nothing. --}}
 <div id="booking-modal"
     data-ads-id="{{ config('services.google_ads.id') }}"
     data-ads-label="{{ config('services.google_ads.booking_label') }}"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 bg-opacity-50 p-4 font-poppins">
     <div class="bg-evergreen p-8 max-w-2xl w-full relative max-h-[90vh] overflow-y-auto">
         <!-- Close Button -->
         <button id="close-modal" class="absolute top-4 right-4 text-ivory hover:text-white transition-colors"
                 aria-label="Close">
             <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <path
                     d="M16.4811 18.9999L9.43032 11.9491C9.09569 11.6151 8.90746 11.1618 8.90705 10.689C8.90663 10.2162 9.09405 9.76255 9.42809 9.42792C9.76213 9.0933 10.2154 8.90507 10.6882 8.90465C11.1611 8.90423 11.6147 9.09166 11.9493 9.4257L19.0001 16.4765L26.0509 9.4257C26.3855 9.09107 26.8393 8.90308 27.3126 8.90308C27.7858 8.90308 28.2397 9.09107 28.5743 9.4257C28.9089 9.76033 29.0969 10.2142 29.0969 10.6874C29.0969 11.1607 28.9089 11.6145 28.5743 11.9491L21.5235 18.9999L28.5743 26.0507C28.9089 26.3853 29.0969 26.8392 29.0969 27.3124C29.0969 27.7857 28.9089 28.2395 28.5743 28.5741C28.2397 28.9088 27.7858 29.0968 27.3126 29.0968C26.8393 29.0968 26.3855 28.9088 26.0509 28.5741L19.0001 21.5234L11.9493 28.5741C11.6147 28.9088 11.1608 29.0968 10.6876 29.0968C10.2143 29.0968 9.76049 28.9088 9.42587 28.5741C9.09124 28.2395 8.90324 27.7857 8.90324 27.3124C8.90324 26.8392 9.09124 26.3853 9.42587 26.0507L16.4811 18.9999Z"
                     fill="white" />
             </svg>
         </button>

         <h2 class="text-ivory font-league-gothic text-2xl md:text-3xl text-center mb-6 uppercase">
             Book an appointment
         </h2>

         {{-- STEP ONE — the phone number, and nothing else.

              Asked first and on its own because it is the detail the shop
              actually reaches people on, and because posting it before the
              rest of the form means a number is kept even from somebody who
              gets this far and then changes their mind. --}}
         <form class="space-y-6" id="booking-identify-form">
             <div>
                 <label for="booking-phone" class="block text-white text-base mb-2 uppercase">Mobile number</label>

                 {{-- The country code is fixed and not typed. It sits inside
                      the field so the number reads as one thing, and it is
                      aria-hidden because the helper text below already says
                      "US mobile number" — a screen reader should not have to
                      hear a flag emoji to understand the field. --}}
                 <div class="field-container p-telfield">
                     <span class="p-telprefix" aria-hidden="true">&#127482;&#127480; +1</span>
                     <input type="tel" id="booking-phone" name="phone" placeholder="(224) 669-8063"
                         inputmode="tel" autocomplete="tel-national"
                         {{-- Ten digits, as "(224) 669-8063" — the formatting
                              is part of the value, so the limit is 14. --}}
                         maxlength="14"
                         aria-describedby="booking-phone-help"
                         class="w-full px-4 py-3 rounded bg-white text-[#080B16]">
                 </div>
                 <p class="text-ivory/60 text-sm mt-2" id="booking-phone-help">
                     US mobile number. We use it to confirm your appointment and to reach
                     you if anything changes.
                 </p>
             </div>

             {{-- Unticked, always. The exact wording is the consent record:
                  what they agreed to has to be what they were shown. --}}
             <label class="booking-consent" for="booking-consent">
                 <input type="checkbox" id="booking-consent" name="sms_marketing_consent" value="1">
                 <span>
                     Yes, text me appointment updates and offers from Nexo Barbershop.
                     Msg &amp; data rates may apply. Reply STOP to opt out.
                 </span>
             </label>

             <button type="submit" id="booking-continue"
                 class="w-full bg-ivory text-black font-league-gothic text-xl md:text-2xl tracking-wider py-3 rounded hover:bg-opacity-90 transition-colors font-bold uppercase">
                 CONTINUE
             </button>
         </form>

         {{-- STEP TWO — everything else. Hidden until step one has answered. --}}
         <form class="space-y-6 hidden" id="booking-form">

             {{-- The number they just gave, shown so they can see it is right,
                  and a way back to step one that does not lose the rest. --}}
             <div class="flex items-center gap-2">
                 <span class="text-ivory/80 text-sm">
                     Booking for <strong id="booking-phone-shown" class="text-ivory"></strong>
                 </span>
                 <button type="button" id="booking-phone-change"
                         class="text-crimson text-sm underline hover:no-underline">change</button>
             </div>

             <!-- Barber -->
             <div>
                 <label for="booking-barber" class="block text-white text-base mb-2 uppercase">Barber</label>
                 <div class="field-container select-wrapper">
                     <select id="booking-barber" name="barber_id"
                         class="w-full px-4 py-3 rounded bg-white text-[#080B16] appearance-none pr-10">
                         <option value="">Any available barber</option>
                         @foreach ($modalBarbers as $barber)
                             <option value="{{ $barber->id }}"
                                 {{ $lockedBarber && $lockedBarber->id === $barber->id ? 'selected' : '' }}>
                                 {{ $barber->name }}
                             </option>
                         @endforeach
                     </select>
                     <div class="select-arrow">
                         <svg class="w-5 h-5 text-evergreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                         </svg>
                     </div>
                 </div>
                 {{-- Shown when a barber was chosen from a "Book with X" button.
                      Locked so the choice cannot be lost by a stray click, with a
                      way out that does not mean closing the modal. --}}
                 <div id="booking-barber-lock" class="hidden mt-2 flex items-center gap-2">
                     <span class="text-ivory/80 text-sm">
                         Booking with <strong id="booking-barber-name" class="text-ivory"></strong>
                     </span>
                     <button type="button" id="booking-barber-change"
                             class="text-crimson text-sm underline hover:no-underline">change</button>
                 </div>
             </div>

             <!-- Date -->
             <div>
                 <label for="booking-date" class="block text-white text-base mb-2 uppercase">Date</label>
                 <div class="field-container">
                     <input type="date" id="booking-date" name="date"
                         class="w-full px-4 py-3 rounded bg-white text-[#080B16]">
                 </div>
             </div>

             <!-- Available time -->
             <div>
                 <label for="booking-time" class="block text-white text-base mb-2 uppercase">Available time</label>
                 <div class="field-container select-wrapper">
                     <select id="booking-time" name="time"
                         class="w-full px-4 py-3 rounded bg-white text-[#080B16] appearance-none pr-10">
                         <option value="">Select a date first</option>
                     </select>
                     <div class="select-arrow">
                         <svg class="w-5 h-5 text-evergreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                         </svg>
                     </div>
                 </div>
                 <p id="booking-time-hint" class="text-ivory/60 text-sm mt-2 hidden"></p>
             </div>

             <!-- Service -->
             <div>
                 <label for="booking-service" class="block text-white text-base mb-2 uppercase">Service type</label>
                 <div class="field-container select-wrapper">
                     <select id="booking-service" name="service_id"
                         class="w-full px-4 py-3 rounded bg-white text-[#080B16] appearance-none pr-10">
                         <option value="">Select service type</option>
                     </select>
                     <div class="select-arrow">
                         <svg class="w-5 h-5 text-evergreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                         </svg>
                     </div>
                 </div>
             </div>

             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <!-- First name -->
                 <div>
                     <label for="booking-first-name" class="block text-white text-base mb-2 uppercase">First name</label>
                     <div class="field-container">
                         <input type="text" id="booking-first-name" name="first_name" placeholder="Omar"
                             autocomplete="given-name"
                             class="w-full px-4 py-3 rounded bg-white text-[#080B16]">
                     </div>
                 </div>
                 <!-- Last name -->
                 <div>
                     <label for="booking-last-name" class="block text-white text-base mb-2 uppercase">Last name</label>
                     <div class="field-container">
                         <input type="text" id="booking-last-name" name="last_name" placeholder="Haddad"
                             autocomplete="family-name"
                             class="w-full px-4 py-3 rounded bg-white text-[#080B16]">
                     </div>
                 </div>
             </div>

             {{-- Optional since 10 Nov 2026. The phone is what the shop needs;
                  an email only buys a confirmation message. --}}
             <div>
                 <label for="booking-email" class="block text-white text-base mb-2 uppercase">
                     Email <span class="text-ivory/60 normal-case">(optional)</span>
                 </label>
                 <div class="field-container">
                     <input type="email" id="booking-email" name="email" placeholder="your@email.com"
                         autocomplete="email"
                         class="w-full px-4 py-3 rounded bg-white text-[#080B16]">
                 </div>
             </div>

             <button type="submit"
                 class="w-full bg-ivory text-black font-league-gothic text-xl md:text-2xl tracking-wider py-3 rounded hover:bg-opacity-90 transition-colors font-bold uppercase">
                 BOOK NOW
             </button>
         </form>
     </div>

     <style>
         /*
          * The consent row. Written out rather than assembled from utility
          * classes: this project ships a pre-built output.css with no build
          * step, so a Tailwind class that is not already in that file does
          * nothing at all, silently.
          */
         .booking-consent {
             display: flex;
             align-items: flex-start;
             gap: 10px;
             cursor: pointer;
             color: rgba(248, 237, 210, 0.85);
             font-size: 14px;
             line-height: 1.5;
         }

         .booking-consent input[type="checkbox"] {
             flex: 0 0 auto;
             width: 20px;
             height: 20px;
             margin-top: 1px;
             accent-color: #8C1C13;
             cursor: pointer;
         }

         /*
          * The fixed country code, inside the field.
          *
          * Absolutely positioned and pointer-events:none so it cannot be
          * selected, focused or deleted — it is a label that happens to sit
          * inside a box, not part of the value. The input is padded past it.
          */
         .p-telfield { position: relative; }

         .p-telprefix {
             position: absolute;
             left: 16px;
             top: 50%;
             transform: translateY(-50%);
             color: #080B16;
             font-size: 16px;
             font-weight: 600;
             line-height: 1;
             white-space: nowrap;
             pointer-events: none;
             z-index: 1;
         }

         /*
          * Specific enough to win regardless of order. The shared rule below
          * sets the padding shorthand on every field, which would otherwise
          * reset this longhand simply by coming later in the file — and the
          * value would sit underneath the prefix.
          */
         .field-container.p-telfield input[type="tel"] { padding-left: 74px; }

         /* Custom styling for ALL form inputs and selects */
         .field-container input[type="text"],
         .field-container input[type="tel"],
         .field-container input[type="email"],
         .field-container input[type="date"],
         .field-container select,
         .time-select {
             appearance: none;
             -webkit-appearance: none;
             -moz-appearance: none;
             width: 100%;
             padding: 12px 16px;
             border-radius: 4px;
             background: white;
             color: #080B16;
             font-size: 16px;
             font-weight: 500;
             border: 2px solid transparent;
             transition: all 0.3s ease;
             box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
         }

         /* Special padding for selects to accommodate arrow */
         .field-container select,
         .time-select {
             padding-right: 40px;
             cursor: pointer;
         }

         /* Hover effects */
         .field-container input:hover,
         .field-container select:hover,
         .time-select:hover {
             border-color: #8C1C13;
             box-shadow: 0 4px 12px rgba(140, 28, 19, 0.15);
         }

         /* Focus effects */
         .field-container input:focus,
         .field-container select:focus,
         .time-select:focus {
             outline: none;
             border-color: #8C1C13;
             box-shadow: 0 0 0 3px rgba(140, 28, 19, 0.1);
         }

         /* Placeholder styling */
         .field-container input::placeholder {
             color: #6B7280;
             opacity: 0.7;
         }

         /* Date input calendar icon */
         .field-container input[type="date"]::-webkit-calendar-picker-indicator {
             cursor: pointer;
             opacity: 0.6;
             transition: opacity 0.3s ease;
         }

         .field-container input[type="date"]:hover::-webkit-calendar-picker-indicator {
             opacity: 1;
         }

         /* Custom arrow container for dropdowns */
         .select-wrapper {
             position: relative;
         }

         .select-arrow {
             position: absolute;
             right: 12px;
             top: 50%;
             transform: translateY(-50%);
             pointer-events: none;
             transition: transform 0.3s ease;
         }

         .select-wrapper:hover .select-arrow {
             transform: translateY(-50%) scale(1.1);
         }

         .select-wrapper select:focus~.select-arrow {
             transform: translateY(-50%) rotate(180deg);
         }

         /* Time select specific styling */
         .time-select-container {
             position: relative;
         }

         .time-select-arrow {
             position: absolute;
             right: 12px;
             top: 50%;
             transform: translateY(-50%);
             pointer-events: none;
             transition: transform 0.3s ease;
         }

         .time-select-container:hover .time-select-arrow {
             transform: translateY(-50%) scale(1.1);
         }

         .time-select:focus~.time-select-arrow {
             transform: translateY(-50%) rotate(180deg);
         }

         /* Time separator styling */
         .time-separator {
             color: #F4EAD5;
             font-size: 28px;
             font-weight: bold;
             margin: 0 4px;
             display: flex;
             align-items: center;
         }

         /* Error state styling */
         .field-container input.ring-2.ring-red-500,
         .field-container select.ring-2.ring-red-500 {
             border-color: #DC2626;
             box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
         }

         /* Responsive adjustments */
         @media (max-width: 640px) {

             .field-container input,
             .field-container select,
             .time-select {
                 padding: 10px 12px;
                 font-size: 14px;
             }

             .field-container select,
             .time-select {
                 padding-right: 35px;
             }

             .time-separator {
                 font-size: 24px;
             }
         }
     </style>
 </div>

 <script>
     // Endpoints and the preselected barber, handed to booking.js. The script
     // is a static asset and cannot call route() itself.
     window.NexoBooking = {
         slotsUrl: @json(route('bookings.booked-slots')),
         storeUrl: @json(route('bookings.store')),
         identifyUrl: @json(route('bookings.identify')),
         servicesUrl: @json(route('services.get')),
         lockedBarberId: {{ $lockedBarber?->id ?? 'null' }},
         lockedBarberName: @json($lockedBarber->name ?? null)
     };
 </script>
