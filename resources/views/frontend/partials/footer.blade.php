 <!-- Footer -->
 <footer class="text-evergreen py-12 px-6 relative z-10 font-league-gothic">
     <!-- Background Pattern -->
     <div class="absolute inset-0 z-0">
         <img src="{{ asset('frontend_assets/assets/images/bg-left.png') }}" alt="Background pattern"
             class="w-full h-full object-cover">
     </div>
     <div class="max-w-7xl mx-auto relative z-10">
         <div class="grid grid-cols-1 md:grid-cols-8 gap-8 mb-8">
             <!-- Left Section - Branding -->
             <div class="space-y-4 col-span-1 md:col-span-4">
                 <a href="{{ route('home') }}">
                     <img src="{{ asset($settings->valueOf('company_logo_footer')) }}" alt="NEXO Log">
                     <!-- <img src="./assets/images/logo.svg" alt="NEXO Log"> -->
                 </a>
                 <p class="text- md:text-3xl font-normal max-w-xl mt-6">
                     {{ $settings->valueOf('site_description') }}
                 </p>
             </div>

             <!-- Center Section - Quick Links -->
             <div class="col-span-1 md:col-span-2">
                 <h3 class="font-league-gothic text-2xl md:text-3xl mb-4 text-crimson">Quick Links</h3>
                 <ul class="space-y-2 text-xl md:text-2xl">
                     <li><a href="{{ route('home') }}" class="hover:text-crimson transition-colors">Home</a></li>
                     <li><a href="{{ route('gallery') }}" class="hover:text-crimson transition-colors">Gallary</a></li>
                     <li><a href="{{ route('services') }}" class="hover:text-crimson transition-colors">Services</a>
                     </li>
                     <li><a href="{{ route('contact') }}" class="hover:text-crimson transition-colors">Contact</a></li>
                 </ul>
             </div>

             <!-- Right Section - Contact Info -->
             <div class="col-span-1 md:col-span-2">
                 <h3 class="font-league-gothic text-2xl md:text-3xl mb-4 text-crimson">Contact</h3>
                 <ul class="space-y-2 text-xl md:text-2xl">
                     <li>{{ $settings->valueOf('phone') }}</li>
                     <li>{{ $settings->valueOf('email') }}</li>
                     <li>{{ $settings->valueOf('address') }}</li>
                 </ul>
             </div>
         </div>
         <div class="flex flex-col md:flex-row items-center justify-between border-t border-[#1B46295E] pt-4">
             <p class="text-xl text-center">
                 &copy; 2026 {{ $settings->valueOf('site_title') }}. All rights reserved.
             </p>
             <!-- Social Media Icons -->
             <div class="flex gap-3 mt-6">
                 <!-- TikTok Icon -->
                 <a href="{{ $settings->valueOf('facebook') }}"
                     class="w-10 h-10 bg-[#1B462926] flex items-center justify-center hover:bg-[#1B46295E] transition-colors">
                     <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path
                             d="M15.0002 1.66667H12.5002C11.3951 1.66667 10.3353 2.10566 9.55388 2.88706C8.77248 3.66846 8.3335 4.72827 8.3335 5.83334V8.33334H5.8335V11.6667H8.3335V18.3333H11.6668V11.6667H14.1668L15.0002 8.33334H11.6668V5.83334C11.6668 5.61232 11.7546 5.40036 11.9109 5.24408C12.0672 5.0878 12.2791 5.00001 12.5002 5.00001H15.0002V1.66667Z"
                             stroke="#8C1C13" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                     </svg>
                 </a>

                 <!-- Instagram Icon -->
                 <a href="{{ $settings->valueOf('instagram') }}"
                     class="w-10 h-10 bg-[#1B462926] flex items-center justify-center hover:bg-[#1B46295E] transition-colors">
                     <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path
                             d="M14.1665 1.66667H5.83317C3.53198 1.66667 1.6665 3.53215 1.6665 5.83334V14.1667C1.6665 16.4679 3.53198 18.3333 5.83317 18.3333H14.1665C16.4677 18.3333 18.3332 16.4679 18.3332 14.1667V5.83334C18.3332 3.53215 16.4677 1.66667 14.1665 1.66667Z"
                             stroke="#8C1C13" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M13.3333 9.47505C13.4361 10.1686 13.3176 10.8769 12.9947 11.4992C12.6718 12.1216 12.1609 12.6262 11.5346 12.9414C10.9083 13.2567 10.1986 13.3664 9.50641 13.255C8.81419 13.1436 8.17472 12.8168 7.67895 12.321C7.18318 11.8253 6.85636 11.1858 6.74497 10.4936C6.63359 9.80135 6.74331 9.09163 7.05852 8.46536C7.37374 7.8391 7.87841 7.32817 8.50074 7.00525C9.12307 6.68234 9.83138 6.56388 10.5249 6.66672C11.2324 6.77162 11.8873 7.10127 12.393 7.60697C12.8987 8.11268 13.2283 8.76762 13.3333 9.47505Z"
                             stroke="#8C1C13" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                         <path d="M14.5835 5.41667H14.5918" stroke="#8C1C13" stroke-width="1.66667"
                             stroke-linecap="round" stroke-linejoin="round" />
                     </svg>
                 </a>

                 <!-- Website/Link Icon -->
                 <a href="{{ $settings->valueOf('twitter') }}"
                     class="w-10 h-10 bg-[#1B462926] flex items-center justify-center hover:bg-[#1B46295E] transition-colors">
                     <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path
                             d="M18.3332 3.33336C18.3332 3.33336 17.7498 5.08336 16.6665 6.16669C17.9998 14.5 8.83317 20.5834 1.6665 15.8334C3.49984 15.9167 5.33317 15.3334 6.6665 14.1667C2.49984 12.9167 0.416504 8.00002 2.49984 4.16669C4.33317 6.33336 7.1665 7.58336 9.99984 7.50002C9.24984 4.00002 13.3332 2.00002 15.8332 4.33336C16.7498 4.33336 18.3332 3.33336 18.3332 3.33336Z"
                             stroke="#8C1C13" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                     </svg>

                 </a>
             </div>
         </div>
     </div>
 </footer>

 <!-- Booking Modal -->
 <div id="booking-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 bg-opacity-50 p-4 font-poppins">
     <div class="bg-evergreen p-8 max-w-2xl w-full relative max-h-[90vh] overflow-y-auto">
         <!-- Close Button -->
         <button id="close-modal" class="absolute top-4 right-4 text-ivory hover:text-white transition-colors">
             <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <path
                     d="M16.4811 18.9999L9.43032 11.9491C9.09569 11.6151 8.90746 11.1618 8.90705 10.689C8.90663 10.2162 9.09405 9.76255 9.42809 9.42792C9.76213 9.0933 10.2154 8.90507 10.6882 8.90465C11.1611 8.90423 11.6147 9.09166 11.9493 9.4257L19.0001 16.4765L26.0509 9.4257C26.3855 9.09107 26.8393 8.90308 27.3126 8.90308C27.7858 8.90308 28.2397 9.09107 28.5743 9.4257C28.9089 9.76033 29.0969 10.2142 29.0969 10.6874C29.0969 11.1607 28.9089 11.6145 28.5743 11.9491L21.5235 18.9999L28.5743 26.0507C28.9089 26.3853 29.0969 26.8392 29.0969 27.3124C29.0969 27.7857 28.9089 28.2395 28.5743 28.5741C28.2397 28.9088 27.7858 29.0968 27.3126 29.0968C26.8393 29.0968 26.3855 28.9088 26.0509 28.5741L19.0001 21.5234L11.9493 28.5741C11.6147 28.9088 11.1608 29.0968 10.6876 29.0968C10.2143 29.0968 9.76049 28.9088 9.42587 28.5741C9.09124 28.2395 8.90324 27.7857 8.90324 27.3124C8.90324 26.8392 9.09124 26.3853 9.42587 26.0507L16.4811 18.9999Z"
                     fill="white" />
             </svg>
         </button>

         <!-- Modal Title -->
         <h2 class="text-ivory font-league-gothic text-2xl md:text-3xl text-center mb-6 uppercase">Book an
             appointment
         </h2>

         <!-- Form -->
         <form class="space-y-6" id="booking-form">
             <!-- Name Field -->
             <div>
                 <label class="block text-white text-base mb-2 uppercase">NAME</label>
                 <div class="field-container">
                     <input type="text" id="booking-name" name="name" placeholder="Name"
                         class="w-full px-4 py-3 rounded bg-white placeholder-[#080B16] focus:outline-none focus:ring-2 focus:ring-ivory">
                 </div>
             </div>

             <!-- Date Field -->
             <div>
                 <label class="block text-white text-base mb-2 uppercase">Determine the date</label>
                 <div class="field-container">
                     <input type="date" id="booking-date" name="date"
                         class="w-full px-4 py-3 rounded bg-white placeholder-[#080B16] focus:outline-none focus:ring-2 focus:ring-ivory">
                 </div>
             </div>

             <!-- Service Type Field -->
             <div>
                 <label class="block text-white text-base mb-2 uppercase">Service type</label>
                 <div class="field-container">
                     <div class="relative">
                         <select id="booking-service" name="service_id"
                             class="w-full px-4 py-3 rounded bg-white text-[#080B16] focus:outline-none focus:ring-2 focus:ring-ivory appearance-none pr-10">
                             <option value="">Select service type</option>
                             <!-- Services will be loaded dynamically -->
                         </select>
                         <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                             <svg class="w-5 h-5 text-evergreen" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                     d="M19 9l-7 7-7-7"></path>
                             </svg>
                         </div>
                     </div>
                 </div>
             </div>

             <!-- Choose an hour -->
             <div>
                 <label class="block text-white text-base mb-2 uppercase">Choose an hour</label>
                 <div class="field-container">
                     <input type="hidden" id="booking-time" name="time" value="">
                     <div class="flex gap-3 flex-wrap text-base" id="hour-buttons-container">
                         <button type="button" data-time="09:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             09:00
                         </button>
                         <button type="button" data-time="10:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             10:00
                         </button>
                         <button type="button" data-time="11:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             11:00
                         </button>
                         <button type="button" data-time="12:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             12:00
                         </button>
                         <button type="button" data-time="13:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             13:00
                         </button>
                         <button type="button" data-time="14:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             14:00
                         </button>
                         <button type="button" data-time="15:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             15:00
                         </button>
                         <button type="button" data-time="16:00"
                             class="hour-btn bg-evergreen text-ivory border border-ivory px-4 py-2 rounded hover:bg-ivory hover:text-evergreen transition-colors">
                             16:00
                         </button>
                     </div>
                 </div>
             </div>

             <!-- Submit Button -->
             <button type="submit"
                 class="w-full bg-ivory text-black font-league-gothic text-xl md:text-2xl tracking-wider py-3 rounded hover:bg-opacity-90 transition-colors font-bold uppercase">
                 BOOK NOW
             </button>
         </form>
     </div>
 </div>

 <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

 <script>
     // Mobile menu toggle
     const mobileMenuButton = document.getElementById('mobile-menu-button');
     const mobileMenu = document.getElementById('mobile-menu');
     const menuIcon = document.getElementById('menu-icon');
     const closeIcon = document.getElementById('close-icon');

     if (mobileMenuButton && mobileMenu) {
         mobileMenuButton.addEventListener('click', function() {
             mobileMenu.classList.toggle('hidden');
             menuIcon.classList.toggle('hidden');
             closeIcon.classList.toggle('hidden');
         });

         // Close mobile menu when clicking on a link
         const mobileMenuLinks = mobileMenu.querySelectorAll('a');
         mobileMenuLinks.forEach(link => {
             link.addEventListener('click', function() {
                 mobileMenu.classList.add('hidden');
                 menuIcon.classList.remove('hidden');
                 closeIcon.classList.add('hidden');
             });
         });
     }

     // Booking Modal
     const bookingModal = document.getElementById('booking-modal');
     const closeModalBtn = document.getElementById('close-modal');
     const bookNowButtons = document.querySelectorAll('.book-now-btn');
     const hourButtons = document.querySelectorAll('.hour-btn');
     const bookingForm = document.getElementById('booking-form');
     const bookingTimeInput = document.getElementById('booking-time');

     // Load services when modal opens
     function loadServices() {
         axios.get('{{ route('services.get') }}')
             .then(function(response) {
                 const serviceSelect = document.getElementById('booking-service');
                 const services = response.data.services;

                 // Clear existing options except the first one
                 serviceSelect.innerHTML = '<option value="">Select service type</option>';

                 // Add services to dropdown
                 services.forEach(service => {
                     const option = document.createElement('option');
                     option.value = service.id;
                     option.textContent = service.name;
                     serviceSelect.appendChild(option);
                 });
             })
             .catch(function(error) {
                 console.error('Error loading services:', error);
                 showNotification('Failed to load services', 'error');
             });
     }

     // Show modal when clicking any BOOK NOW button
     bookNowButtons.forEach(button => {
         button.addEventListener('click', function(e) {
             e.preventDefault();
             loadServices(); // Load services when modal opens
             bookingModal.classList.remove('hidden');
             setMinDate(); // Set minimum date to today
         });
     });

     // Set minimum date to today
     function setMinDate() {
         const dateInput = document.getElementById('booking-date');
         const today = new Date().toISOString().split('T')[0];
         dateInput.setAttribute('min', today);
         dateInput.value = today;
     }

     // Close modal when clicking close button
     closeModalBtn.addEventListener('click', function() {
         bookingModal.classList.add('hidden');
         clearBookingForm();
     });

     // Close modal when clicking outside
     bookingModal.addEventListener('click', function(e) {
         if (e.target === bookingModal) {
             bookingModal.classList.add('hidden');
             clearBookingForm();
         }
     });

     // Hour selection
     hourButtons.forEach(button => {
         button.addEventListener('click', function() {
             // Remove validation error if exists
             const timeContainer = document.getElementById('hour-buttons-container').parentElement;
             const existingError = timeContainer.querySelector('.validation-error');
             if (existingError) {
                 existingError.remove();
             }

             // Update selected hour
             hourButtons.forEach(btn => {
                 btn.classList.remove('selected', 'bg-ivory', 'text-evergreen');
                 btn.classList.add('bg-evergreen', 'text-ivory', 'border', 'border-ivory');
             });
             this.classList.add('selected', 'bg-ivory', 'text-evergreen');
             this.classList.remove('bg-evergreen', 'text-ivory', 'border', 'border-ivory');

             // Set the time value
             bookingTimeInput.value = this.getAttribute('data-time');
         });
     });

     // Clear validation errors
     function clearValidationErrors() {
         const errorElements = document.querySelectorAll('.validation-error');
         errorElements.forEach(el => el.remove());

         const inputs = bookingForm.querySelectorAll('input, select');
         inputs.forEach(input => {
             input.classList.remove('ring-2', 'ring-red-500');
         });
     }

     // Show field validation error
     function showFieldError(fieldId, message) {
         const field = document.getElementById(fieldId);
         if (!field) return;

         const fieldContainer = field.closest('.field-container');
         if (!fieldContainer) return;

         // Add error styling
         field.classList.add('ring-2', 'ring-red-500');

         // Create error message element
         const errorDiv = document.createElement('div');
         errorDiv.className = 'validation-error text-red-500 text-sm mt-2';
         errorDiv.textContent = message;

         // Insert after the field container
         fieldContainer.appendChild(errorDiv);

         // Remove error when user interacts
         field.addEventListener('change', function() {
             field.classList.remove('ring-2', 'ring-red-500');
             const error = fieldContainer.querySelector('.validation-error');
             if (error) error.remove();
         }, {
             once: true
         });
     }

     // Notification function
     function showNotification(message, type) {
         const notification = document.createElement('div');
         notification.className = `fixed top-5 right-5 px-6 py-4 rounded shadow-lg z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
        }`;
         notification.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                    }
                </svg>
                <span class="font-league-gothic text-lg tracking-wider">${message}</span>
            </div>
        `;

         document.body.appendChild(notification);

         setTimeout(() => {
             notification.style.transform = 'translateX(0)';
         }, 10);

         setTimeout(() => {
             notification.style.transform = 'translateX(400px)';
             notification.style.opacity = '0';
             setTimeout(() => {
                 notification.remove();
             }, 300);
         }, 5000);
     }

     // Clear booking form
     function clearBookingForm() {
         bookingForm.reset();
         bookingTimeInput.value = '';
         hourButtons.forEach(btn => {
             btn.classList.remove('selected', 'bg-ivory', 'text-evergreen');
             btn.classList.add('bg-evergreen', 'text-ivory', 'border', 'border-ivory');
         });
         clearValidationErrors();
     }

     // Form submission
     bookingForm.addEventListener('submit', function(e) {
         e.preventDefault();

         // Clear previous validation errors
         clearValidationErrors();

         // Get form data
         const formData = {
             name: document.getElementById('booking-name').value,
             date: document.getElementById('booking-date').value,
             service_id: document.getElementById('booking-service').value,
             time: bookingTimeInput.value,
             _token: '{{ csrf_token() }}'
         };

         // Client-side validation
         let hasErrors = false;
         const fields = {
             'booking-name': {
                 message: 'The name field is required.',
                 value: formData.name
             },
             'booking-date': {
                 message: 'The date field is required.',
                 value: formData.date
             },
             'booking-service': {
                 message: 'Please select a service type.',
                 value: formData.service_id
             },
             'booking-time': {
                 message: 'Please select an hour.',
                 value: formData.time
             }
         };

         for (const [fieldId, fieldData] of Object.entries(fields)) {
             if (!fieldData.value || fieldData.value.trim() === '') {
                 showFieldError(fieldId, fieldData.message);
                 hasErrors = true;
             }
         }

         if (hasErrors) {
             return;
         }

         // Disable submit button
         const submitButton = bookingForm.querySelector('button[type="submit"]');
         const originalText = submitButton.textContent;
         submitButton.disabled = true;
         submitButton.textContent = 'BOOKING...';

         // Send request using Axios
         axios.post('{{ route('bookings.store') }}', formData)
             .then(function(response) {
                 showNotification('Booking confirmed successfully!', 'success');
                 clearBookingForm();
                 bookingModal.classList.add('hidden');
             })
             .catch(function(error) {
                 if (error.response && error.response.status === 422) {
                     // Validation errors from server
                     const errors = error.response.data.errors;
                     for (const [field, messages] of Object.entries(errors)) {
                         const fieldId = 'booking-' + field.replace('_', '-');
                         showFieldError(fieldId, messages[0]);
                     }
                 } else {
                     const errorMessage = error.response?.data?.message ||
                         'An error occurred. Please try again.';
                     showNotification(errorMessage, 'error');
                 }
             })
             .finally(function() {
                 submitButton.disabled = false;
                 submitButton.textContent = originalText;
             });
     });
 </script>
