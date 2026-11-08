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
 <div id="booking-modal"
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

         <form class="space-y-6" id="booking-form">
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
                 <!-- Name -->
                 <div>
                     <label for="booking-name" class="block text-white text-base mb-2 uppercase">Name</label>
                     <div class="field-container">
                         <input type="text" id="booking-name" name="name" placeholder="Name"
                             class="w-full px-4 py-3 rounded bg-white text-[#080B16]">
                     </div>
                 </div>
                 <!-- Email -->
                 <div>
                     <label for="booking-email" class="block text-white text-base mb-2 uppercase">Email</label>
                     <div class="field-container">
                         <input type="email" id="booking-email" name="email" placeholder="your@email.com"
                             class="w-full px-4 py-3 rounded bg-white text-[#080B16]">
                     </div>
                 </div>
             </div>

             <button type="submit"
                 class="w-full bg-ivory text-black font-league-gothic text-xl md:text-2xl tracking-wider py-3 rounded hover:bg-opacity-90 transition-colors font-bold uppercase">
                 BOOK NOW
             </button>
         </form>
     </div>

     <style>
         /* Custom styling for ALL form inputs and selects */
         .field-container input[type="text"],
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
         servicesUrl: @json(route('services.get')),
         lockedBarberId: {{ $lockedBarber?->id ?? 'null' }},
         lockedBarberName: @json($lockedBarber->name ?? null)
     };
 </script>
