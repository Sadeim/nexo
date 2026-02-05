@extends('frontend.layouts.app')

@push('styles')
@endpush

@section('content')
    <main>
        <!-- Hero Start -->
        <section class="relative py-16 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0">
                <img src="{{ asset($settings->valueOf('green_background')) }}" alt="Background pattern"
                    class="w-full h-full object-cover">
            </div>

            <div class="container mx-auto px-4 md:px-8 lg:px-16 relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-6 w-full">
                        <h2
                            class="flex-1 font-league-gothic text-ivory text-3xl lg:text-4xl uppercase block whitespace-nowrap">
                            Contact us
                        </h2>
                        <div class="h-px bg-ivory w-full"></div>
                        <svg width="42" height="71" viewBox="0 0 42 71" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19.773 4.4375H25.2727C28.7461 4.4375 31.5516 7.25531 31.5516 10.6944V10.7388H31.6406C34.6465 10.7388 37.0734 13.1572 37.0734 16.1525C37.0734 19.1478 34.6465 21.5663 31.6406 21.5663H24.293L13.6055 26.7138V21.5663H13.4719C10.466 21.5663 8.03906 19.1478 8.03906 16.1525C8.03906 13.1572 10.466 10.7388 13.4719 10.7388H13.4941V10.6944C13.4941 7.23313 16.2996 4.4375 19.773 4.4375ZM31.6629 30.4413L13.6055 39.1609V43.9534L31.6629 35.2559V30.4413ZM31.6406 55.7794H24.7984L31.6629 52.4734V47.6809L14.8501 55.7794H13.4719C10.466 55.7794 8.03906 58.1978 8.03906 61.1931C8.03906 64.1884 10.466 66.6069 13.4719 66.6069H31.6406C34.6465 66.6069 37.0734 64.1884 37.0734 61.1931C37.0734 58.1978 34.6465 55.7794 31.6406 55.7794Z"
                                fill="#F8EDD2" />
                            <path
                                d="M13.6055 30.53L31.6629 21.8325V26.625L13.6055 35.3225V30.53ZM13.6055 47.7697L31.6629 39.0722V43.8647L13.6055 52.5622V47.7697Z"
                                fill="#F8EDD2" />
                        </svg>
                    </div>
                </div>

                <div class="container mx-auto flex flex-col">
                    <div class="flex flex-col xl:flex-row items-center justify-center gap-10 xl:gap-0">
                        <div class="max-w-182.25 max-h-155.75 overflow-hidden relative mx-auto xl:mx-0">
                            <img src="{{ asset($sections['contact_page']->image) }}" alt="Contact us"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 transition-all duration-300 bg-ivory/25">
                                <img src="{{ asset('frontend_assets/assets/images/image 5.png') }}" alt="Contact us"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>

                        <form
                            class="flex flex-col gap-4 bg-white px-4 py-10 w-full max-w-182.25 xl:max-w-154 xl:-ml-16 z-10 mx-auto xl:mx-0">
                            <div class="flex flex-col md:flex-row gap-4 md:gap-8 w-full">
                                <div class="flex flex-col gap-4 w-full">
                                    <input type="text" id="name" name="name" placeholder="Name"
                                        class="w-full p-2 border-b-2 border-[#283327] placeholder:text-[#283327] placeholder:font-league-gothic placeholder:tracking-wider placeholder:font-normal placeholder:text-base uppercase">
                                </div>
                                <div class="flex flex-col gap-4 w-full">
                                    <input type="email" id="email" name="email" placeholder="Email"
                                        class="w-full p-2 border-b-2 border-[#283327] placeholder:text-[#283327] placeholder:font-league-gothic placeholder:tracking-wider placeholder:font-normal placeholder:text-base uppercase">
                                </div>
                            </div>
                            <div class="flex flex-col md:flex-row justify-between gap-4">
                                <div class="flex flex-col w-full">
                                    <input type="text" id="subject" name="subject" placeholder="Subject"
                                        class="w-full p-2 border-b-2 border-[#283327] placeholder:text-[#283327] placeholder:font-league-gothic placeholder:tracking-wider placeholder:font-normal placeholder:text-base uppercase">
                                </div>
                            </div>
                            <div class="flex flex-col w-full">
                                <textarea id="message" name="message" placeholder="Message"
                                    class="w-full p-2 border-b-2 border-[#283327] placeholder:text-[#283327] placeholder:font-league-gothic placeholder:tracking-wider placeholder:font-normal placeholder:text-base uppercase resize-none"></textarea>
                            </div>
                            <button type="submit"
                                class="bg-evergreen text-white px-4 md:px-10 py-2 md:py-3 my-10 uppercase text-lg font-league-gothic tracking-wider w-full md:w-1/2">Send
                                Message</button>
                        </form>
                    </div>

                    <!-- Contact Us Header -->
                    <div class="container mx-auto relative z-10 pt-8">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-6 w-full px-4">
                                <h2
                                    class="font-league-gothic text-ivory text-3xl lg:text-4xl uppercase block whitespace-nowrap">
                                    Contact Us
                                </h2>
                                <div class="h-px bg-ivory w-full"></div>
                                <svg width="42" height="71" viewBox="0 0 42 71" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.773 4.4375H25.2727C28.7461 4.4375 31.5516 7.25531 31.5516 10.6944V10.7388H31.6406C34.6465 10.7388 37.0734 13.1572 37.0734 16.1525C37.0734 19.1478 34.6465 21.5663 31.6406 21.5663H24.293L13.6055 26.7138V21.5663H13.4719C10.466 21.5663 8.03906 19.1478 8.03906 16.1525C8.03906 13.1572 10.466 10.7388 13.4719 10.7388H13.4941V10.6944C13.4941 7.23313 16.2996 4.4375 19.773 4.4375ZM31.6629 30.4413L13.6055 39.1609V43.9534L31.6629 35.2559V30.4413ZM31.6406 55.7794H24.7984L31.6629 52.4734V47.6809L14.8501 55.7794H13.4719C10.466 55.7794 8.03906 58.1978 8.03906 61.1931C8.03906 64.1884 10.466 66.6069 13.4719 66.6069H31.6406C34.6465 66.6069 37.0734 64.1884 37.0734 61.1931C37.0734 58.1978 34.6465 55.7794 31.6406 55.7794Z"
                                        fill="#F8EDD2" />
                                    <path
                                        d="M13.6055 30.53L31.6629 21.8325V26.625L13.6055 35.3225V30.53ZM13.6055 47.7697L31.6629 39.0722V43.8647L13.6055 52.5622V47.7697Z"
                                        fill="#F8EDD2" />
                                </svg>
                            </div>
                        </div>

                        <!-- Contact Info Grid -->
                        <div class="grid xl:grid-cols-3 gap-8 items-start pt-14 md:pt-24">

                            <!-- Center - NEXO Logo -->
                            <div
                                class="text-end flex items-end justify-end mt-0 md:mt-auto mb-10 max-h-[160px] block xl:hidden">
                                <img src="{{ asset('frontend_assets/assets/images/nexo-standard.svg') }}"
                                    alt="X Scissors Logo" class="w-96 h-[165px] mx-auto">
                            </div>

                            <!-- Large X Scissors Logo Left -->
                            <div class="flex items-center justify-center mt-auto">
                                <img src="{{ asset('frontend_assets/assets/images/xLeft.svg') }}" alt="X Scissors Logo"
                                    class="w-full h-full object-cover max-w-42 xl:max-w-52 max-h-66 z-10 -mt-[48px] xl:-mt-[58px]">
                                <!-- Address -->
                                <div
                                    class="flex items-start text-ivory max-w-62 xl:max-w-72 h-30 xl:h-[150px] w-full -ml-14 xl:-ml-18">
                                    <div
                                        class="border border-ivory p-4 px-8 flex flex-col items-center justify-center gap-4 w-full h-full">
                                        <svg width="20" height="28" viewBox="0 0 20 28" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9.8 0C4.382 0 0 4.382 0 9.8C0 15.638 6.188 23.688 8.736 26.754C9.296 27.426 10.318 27.426 10.878 26.754C13.412 23.688 19.6 15.638 19.6 9.8C19.6 4.382 15.218 0 9.8 0ZM9.8 13.3C7.868 13.3 6.3 11.732 6.3 9.8C6.3 7.868 7.868 6.3 9.8 6.3C11.732 6.3 13.3 7.868 13.3 9.8C13.3 11.732 11.732 13.3 9.8 13.3Z"
                                                fill="#F8EDD2" />
                                        </svg>
                                        <div class="font-league-gothic text-lg xl:text-2xl text-center">
                                            <p>{{ $settings->valueOf('address') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Center - NEXO Logo -->
                            <div
                                class="text-end flex items-end justify-end mt-0 md:mt-auto mb-6 md:mb-0 max-h-[160px] hidden xl:block">
                                <img src="{{ asset('frontend_assets/assets/images/nexo-standard.svg') }}"
                                    alt="X Scissors Logo" class="w-96 h-[165px] mx-auto">
                                <!-- <img src="./assets/images/nexo-higher-standard.svg" alt="X Scissors Logo"
                                        class="w-96 h-48 opacity-80 mx-auto my-auto"> -->
                            </div>

                            <!-- Large X Scissors Logo Right -->
                            <div class="flex items-center justify-center">
                                <!-- Address -->
                                <div class="flex items-start text-ivory h-[150px] max-w-72 w-full -mr-24">
                                    <div
                                        class="border border-ivory px-2 2xl:p-4 flex flex-col items-start justify-center gap-1 w-6/7 h-full mt-[9px] xl:mt-[29.5px]">
                                        <!-- Phone and Website -->
                                        <div class="space-y-2 font-league-gothic text-base xl:text-xl">
                                            <div class="flex items-center gap-2">
                                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M18.5031 15.2083L16.3864 14.9667C15.8781 14.9083 15.3781 15.0833 15.0197 15.4417L13.4864 16.975C11.1281 15.775 9.19473 13.85 7.99473 11.4833L9.53639 9.94167C9.89473 9.58333 10.0697 9.08333 10.0114 8.575L9.76973 6.475C9.66973 5.63333 8.96139 5 8.11139 5H6.66973C5.72806 5 4.94473 5.78333 5.00306 6.725C5.44473 13.8417 11.1364 19.525 18.2447 19.9667C19.1864 20.025 19.9697 19.2417 19.9697 18.3V16.8583C19.9781 16.0167 19.3447 15.3083 18.5031 15.2083Z"
                                                        fill="#F8EDD2" />
                                                </svg>
                                                <span>{{ $settings->valueOf('phone') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M19.1665 5.83331H5.83317C4.9165 5.83331 4.1665 6.58331 4.1665 7.49998V17.5C4.1665 18.4166 4.9165 19.1666 5.83317 19.1666H19.1665C20.0832 19.1666 20.8332 18.4166 20.8332 17.5V7.49998C20.8332 6.58331 20.0832 5.83331 19.1665 5.83331ZM18.8332 9.37498L13.3832 12.7833C12.8415 13.125 12.1582 13.125 11.6165 12.7833L6.1665 9.37498C5.95817 9.24165 5.83317 9.01665 5.83317 8.77498C5.83317 8.21665 6.4415 7.88331 6.9165 8.17498L12.4998 11.6666L18.0832 8.17498C18.5582 7.88331 19.1665 8.21665 19.1665 8.77498C19.1665 9.01665 19.0415 9.24165 18.8332 9.37498Z"
                                                        fill="#F8EDD2" />
                                                </svg>

                                                <span>{{ $settings->valueOf('email') }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12.4915 4.16669C7.8915 4.16669 4.1665 7.90002 4.1665 12.5C4.1665 17.1 7.8915 20.8334 12.4915 20.8334C17.0998 20.8334 20.8332 17.1 20.8332 12.5C20.8332 7.90002 17.0998 4.16669 12.4915 4.16669ZM12.4998 19.1667C8.8165 19.1667 5.83317 16.1834 5.83317 12.5C5.83317 8.81669 8.8165 5.83335 12.4998 5.83335C16.1832 5.83335 19.1665 8.81669 19.1665 12.5C19.1665 16.1834 16.1832 19.1667 12.4998 19.1667ZM12.3165 8.33335H12.2665C11.9332 8.33335 11.6665 8.60002 11.6665 8.93335V12.8667C11.6665 13.1584 11.8165 13.4334 12.0748 13.5834L15.5332 15.6584C15.8165 15.825 16.1832 15.7417 16.3498 15.4584C16.5248 15.175 16.4332 14.8 16.1415 14.6334L12.9165 12.7167V8.93335C12.9165 8.60002 12.6498 8.33335 12.3165 8.33335Z"
                                                    fill="#F8EDD2" />
                                            </svg>

                                            <!-- Hours -->
                                            <div class="font-league-gothic text-base xl:text-xl">
                                                <p>{{ $settings->valueOf('hours_part1') }}</span></p>
                                                <p>{{ $settings->valueOf('hours_part2') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <img src="{{ asset('frontend_assets/assets/images/xRight.svg') }}" alt="X Scissors Logo"
                                    class="w-full h-full object-cover max-w-42 xl:max-w-52 max-h-66 z-10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.querySelector('form');

            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear previous validation errors
                clearValidationErrors();

                // Get form data
                const formData = {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    subject: document.getElementById('subject').value,
                    message: document.getElementById('message').value,
                    _token: '{{ csrf_token() }}'
                };

                // Client-side validation - check for empty fields
                let hasErrors = false;
                const fields = {
                    'name': 'The name field is required.',
                    'email': 'The email field is required.',
                    'subject': 'The subject field is required.',
                    'message': 'The message field is required.'
                };

                for (const [fieldName, errorMessage] of Object.entries(fields)) {
                    if (!formData[fieldName] || formData[fieldName].trim() === '') {
                        showFieldError(fieldName, errorMessage);
                        hasErrors = true;
                    }
                }

                // If there are validation errors, don't submit
                if (hasErrors) {
                    return;
                }

                // Disable submit button to prevent double submission
                const submitButton = contactForm.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.textContent = 'Sending...';

                // Send request using Axios
                axios.post('{{ route('contact.submit') }}', formData)
                    .then(function(response) {
                        // Show success notification
                        showNotification('Message sent successfully!', 'success');

                        // Reset form
                        contactForm.reset();
                    })
                    .catch(function(error) {
                        if (error.response && error.response.status === 400) {
                            // Server validation error - show below fields
                            const errorMessage = error.response.data.message;
                            showServerError(errorMessage);
                        } else {
                            // Other errors - show notification
                            const errorMessage = error.response?.data?.message ||
                                'An error occurred. Please try again.';
                            showNotification(errorMessage, 'error');
                        }
                    })
                    .finally(function() {
                        // Re-enable submit button
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    });
            });

            // Clear validation errors
            function clearValidationErrors() {
                const errorElements = document.querySelectorAll('.validation-error');
                errorElements.forEach(el => el.remove());

                // Remove red border from inputs
                const inputs = contactForm.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    input.classList.remove('border-red-500');
                });
            }

            // Show field validation error (accepts field name and message)
            function showFieldError(fieldName, message) {
                const field = document.getElementById(fieldName);
                if (!field) return;

                const fieldContainer = field.parentElement;

                // Add red border to field
                field.classList.add('border-red-500');

                // Create error message element
                const errorDiv = document.createElement('div');
                errorDiv.className = 'validation-error text-red-600 text-sm mt-1 font-league-gothic';
                errorDiv.textContent = message;

                // Insert after the field
                fieldContainer.appendChild(errorDiv);

                // Remove error when user starts typing
                field.addEventListener('input', function() {
                    field.classList.remove('border-red-500');
                    const error = fieldContainer.querySelector('.validation-error');
                    if (error) error.remove();
                }, {
                    once: true
                });
            }

            // Show server validation error
            function showServerError(message) {
                // Determine which field has the error
                let fieldName = '';
                if (message.toLowerCase().includes('name')) {
                    fieldName = 'name';
                } else if (message.toLowerCase().includes('email')) {
                    fieldName = 'email';
                } else if (message.toLowerCase().includes('subject')) {
                    fieldName = 'subject';
                } else if (message.toLowerCase().includes('message')) {
                    fieldName = 'message';
                }

                if (fieldName) {
                    showFieldError(fieldName, message);
                }
            }

            // Notification function
            function showNotification(message, type) {
                // Create notification element
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

                // Add to page
                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 10);

                // Remove after 5 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(400px)';
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 5000);
            }
        });
    </script>
@endpush
