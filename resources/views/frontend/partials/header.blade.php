    <!-- Top Header Bar -->
    <header>
        <div class="bg-crimson text-white py-4 overflow-hidden px-4 md:px-8 lg:px-12 lg:pl-18 ">
            <div class="relative w-full container mx-auto">
                <div
                    class="flex items-center justify-center lg:justify-between gap-12 whitespace-nowrap font-inter font-bold text-xs">
                    <!-- Left -->
                    <div class="flex items-center justify-center gap-4 lg:justify-start">
                        @php
                            $headerText = $settings->valueOf('top_header_description');
                            // Split at the first digit
                            preg_match('/^(.*?)(\d.*)$/', $headerText, $matches);
                            $firstPart = trim($matches[1] ?? '');
                            $secondPart = trim($matches[2] ?? '');
                        @endphp

                        <span class="tracking-wider">{{ $firstPart }}</span>
                        <span class="tracking-wide">{{ $secondPart }}</span>
                    </div>

                    <div class=" items-center gap-8 hidden md:flex">
                        <!-- Location -->
                        <div class="flex items-center gap-2">
                            <!-- svg -->
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 1.25C14.0928 1.25 16.0362 1.92821 17.4629 3.26562C18.8971 4.61021 19.75 6.56633 19.75 9C19.75 12.5033 17.8844 15.9143 16.1094 18.3838C15.2121 19.6321 14.3161 20.6698 13.6445 21.3955C13.3085 21.7587 13.0272 22.0447 12.8291 22.2412C12.73 22.3395 12.6511 22.4158 12.5967 22.4678C12.5698 22.4935 12.5489 22.5135 12.5342 22.5273C12.5268 22.5343 12.5207 22.5401 12.5166 22.5439C12.5147 22.5457 12.5129 22.5468 12.5117 22.5479L12.5098 22.5498L12 22L12.5098 22.5508C12.2223 22.8168 11.7777 22.8168 11.4902 22.5508L12 22L11.4902 22.5498L11.4883 22.5479C11.4871 22.5468 11.4853 22.5457 11.4834 22.5439C11.4793 22.5401 11.4732 22.5343 11.4658 22.5273C11.4511 22.5135 11.4302 22.4935 11.4033 22.4678C11.3489 22.4158 11.27 22.3395 11.1709 22.2412C10.9728 22.0447 10.6915 21.7587 10.3555 21.3955C9.68391 20.6698 8.78787 19.6321 7.89062 18.3838C6.11564 15.9143 4.25 12.5033 4.25 9C4.25 6.55563 5.10255 4.59835 6.53809 3.25586C7.96562 1.92096 9.90909 1.25 12 1.25ZM12 2.75C10.225 2.75 8.66844 3.31733 7.5625 4.35156C6.46453 5.37836 5.75 6.92152 5.75 9C5.75 12.0195 7.3844 15.109 9.10938 17.5088C9.96197 18.6949 10.8162 19.6845 11.457 20.377C11.6625 20.599 11.8463 20.7895 12 20.9463C12.1537 20.7895 12.3375 20.599 12.543 20.377C13.1838 19.6845 14.038 18.6949 14.8906 17.5088C16.6156 15.109 18.25 12.0195 18.25 9C18.25 6.93382 17.5357 5.38978 16.4365 4.35938C15.3297 3.32197 13.773 2.75 12 2.75ZM12 5.25C14.0711 5.25 15.75 6.92893 15.75 9C15.75 11.0711 14.0711 12.75 12 12.75C9.92893 12.75 8.25 11.0711 8.25 9C8.25 6.92893 9.92893 5.25 12 5.25ZM12 6.75C10.7574 6.75 9.75 7.75736 9.75 9C9.75 10.2426 10.7574 11.25 12 11.25C13.2426 11.25 14.25 10.2426 14.25 9C14.25 7.75736 13.2426 6.75 12 6.75Z"
                                    fill="white" />
                            </svg>
                            <span>{{ $settings->valueOf('address') }}</span>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-center gap-2">
                            <!-- svg -->
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7.48801 3.28143L10.4999 6.47732C10.8732 6.87363 10.8734 7.49463 10.5005 7.89127L9.04455 9.43982C9.95876 12.2348 12.0032 14.4708 14.5944 15.5281L14.8845 15.6407L16.2646 14.1729C16.6523 13.7606 17.2985 13.7427 17.708 14.133L20.7199 17.3289C21.0931 17.7252 21.0934 18.3462 20.7205 18.7428L19.9715 19.5395C18.6035 20.9944 16.5203 21.4045 14.6906 20.5789C12.1042 19.412 10.0172 17.9857 8.42964 16.3C6.84206 14.6143 5.49814 12.3976 4.39787 9.65009C3.65062 7.78412 3.97473 5.66885 5.21213 4.21538L5.37155 4.03721L6.04467 3.32128C6.43231 2.90899 7.07851 2.89115 7.48801 3.28143Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>{{ $settings->valueOf('phone') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="bg-evergreen text-white py-4 px-4 md:px-8 lg:px-12 lg:pl-18">
            <div class="flex items-center justify-between container mx-auto">
                <!-- Logo -->
                <a href="{{ route('home') }}">
                    <img src="{{ asset('frontend_assets/assets/images/logoHeader.svg') }}" alt="NEXO Log">
                    {{-- <img src="{{ asset($settings->valueOf('company_logo'))}}" alt="NEXO Log"> --}}
                </a>

                <!-- Desktop Navigation -->
                {{-- <div class="hidden md:flex items-center gap-8">
                    <!-- Nav Items -->
                    <ul class="flex items-center gap-8 text-ivory font-league-gothic text-xl tracking-wider">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors border-b-4">HOME</a>
                        </li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">SERVICES</a>
                        </li>
                        <li><a href="{{ route('gallery') }} " class="hover:text-white transition-colors">GALLERY</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">CONTACT US</a>
                        </li>
                    </ul>

                    <button
                        class="book-now-btn bg-ivory text-evergreen font-league-gothic text-xl tracking-wider p-2 flex items-center gap-3 hover:bg-white transition-colors">
                        BOOK NOW
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.0001 3.25C7.19901 3.25 7.38978 3.32902 7.53043 3.46967C7.67108 3.61032 7.7501 3.80109 7.7501 4V5.668C10.5779 5.41664 13.4223 5.41664 16.2501 5.668V4C16.2501 3.80109 16.3291 3.61032 16.4698 3.46967C16.6104 3.32902 16.8012 3.25 17.0001 3.25C17.199 3.25 17.3898 3.32902 17.5304 3.46967C17.6711 3.61032 17.7501 3.80109 17.7501 4V5.816C18.4796 5.91945 19.1553 6.25882 19.6739 6.78228C20.1924 7.30573 20.5255 7.98452 20.6221 8.715L20.7091 9.368C21.0731 12.114 21.0411 14.898 20.6151 17.636C20.5124 18.2951 20.1936 18.9015 19.7089 19.3598C19.2242 19.8181 18.6009 20.1024 17.9371 20.168L16.7441 20.286C13.5891 20.5967 10.4111 20.5967 7.2561 20.286L6.0631 20.168C5.39925 20.1024 4.77601 19.8181 4.29128 19.3598C3.80655 18.9015 3.4878 18.2951 3.3851 17.636C2.95901 14.8986 2.92736 12.1143 3.2911 9.368L3.3781 8.715C3.47472 7.98452 3.80775 7.30573 4.32633 6.78228C4.84491 6.25882 5.52056 5.91945 6.2501 5.816V4C6.2501 3.80109 6.32912 3.61032 6.46977 3.46967C6.61042 3.32902 6.80119 3.25 7.0001 3.25ZM7.4451 7.203C10.4751 6.904 13.5251 6.904 16.5551 7.203L17.4601 7.293C18.3271 7.378 19.0201 8.049 19.1351 8.912L19.2221 9.565C19.2521 9.793 19.2794 10.0213 19.3041 10.25H4.6961C4.72077 10.0213 4.7481 9.793 4.7781 9.565L4.8651 8.912C4.9206 8.49261 5.11629 8.10431 5.42038 7.8102C5.72447 7.5161 6.11909 7.33347 6.5401 7.292L7.4451 7.203ZM4.5771 11.75C4.47892 13.64 4.57611 15.535 4.8671 17.405C4.91864 17.7356 5.0785 18.0396 5.32157 18.2695C5.56465 18.4994 5.87718 18.642 6.2101 18.675L7.4031 18.793C10.4601 19.095 13.5401 19.095 16.5971 18.793L17.7901 18.675C18.123 18.642 18.4355 18.4994 18.6786 18.2695C18.9217 18.0396 19.0816 17.7356 19.1331 17.405C19.4251 15.533 19.5211 13.638 19.4231 11.75H4.5771Z"
                                fill="#283326" />
                        </svg>
                    </button>
                </div> --}}

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <!-- Nav Items -->
                    <ul class="flex items-center gap-8 text-ivory font-league-gothic text-xl tracking-wider">
                        <li>
                            <a href="{{ route('home') }}"
                                class="hover:text-white transition-colors {{ request()->routeIs('home') ? 'border-b-4 border-ivory' : '' }}">
                                HOME
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('services') }}"
                                class="hover:text-white transition-colors {{ request()->routeIs('services') ? 'border-b-4 border-ivory' : '' }}">
                                SERVICES
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('gallery') }}"
                                class="hover:text-white transition-colors {{ request()->routeIs('gallery') ? 'border-b-4 border-ivory' : '' }}">
                                GALLERY
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}"
                                class="hover:text-white transition-colors {{ request()->routeIs('contact') ? 'border-b-4 border-ivory' : '' }}">
                                CONTACT US
                            </a>
                        </li>
                    </ul>

                    <button
                        class="book-now-btn bg-ivory text-evergreen font-league-gothic text-xl tracking-wider p-2 flex items-center gap-3 hover:bg-white transition-colors">
                        BOOK NOW
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.0001 3.25C7.19901 3.25 7.38978 3.32902 7.53043 3.46967C7.67108 3.61032 7.7501 3.80109 7.7501 4V5.668C10.5779 5.41664 13.4223 5.41664 16.2501 5.668V4C16.2501 3.80109 16.3291 3.61032 16.4698 3.46967C16.6104 3.32902 16.8012 3.25 17.0001 3.25C17.199 3.25 17.3898 3.32902 17.5304 3.46967C17.6711 3.61032 17.7501 3.80109 17.7501 4V5.816C18.4796 5.91945 19.1553 6.25882 19.6739 6.78228C20.1924 7.30573 20.5255 7.98452 20.6221 8.715L20.7091 9.368C21.0731 12.114 21.0411 14.898 20.6151 17.636C20.5124 18.2951 20.1936 18.9015 19.7089 19.3598C19.2242 19.8181 18.6009 20.1024 17.9371 20.168L16.7441 20.286C13.5891 20.5967 10.4111 20.5967 7.2561 20.286L6.0631 20.168C5.39925 20.1024 4.77601 19.8181 4.29128 19.3598C3.80655 18.9015 3.4878 18.2951 3.3851 17.636C2.95901 14.8986 2.92736 12.1143 3.2911 9.368L3.3781 8.715C3.47472 7.98452 3.80775 7.30573 4.32633 6.78228C4.84491 6.25882 5.52056 5.91945 6.2501 5.816V4C6.2501 3.80109 6.32912 3.61032 6.46977 3.46967C6.61042 3.32902 6.80119 3.25 7.0001 3.25ZM7.4451 7.203C10.4751 6.904 13.5251 6.904 16.5551 7.203L17.4601 7.293C18.3271 7.378 19.0201 8.049 19.1351 8.912L19.2221 9.565C19.2521 9.793 19.2794 10.0213 19.3041 10.25H4.6961C4.72077 10.0213 4.7481 9.793 4.7781 9.565L4.8651 8.912C4.9206 8.49261 5.11629 8.10431 5.42038 7.8102C5.72447 7.5161 6.11909 7.33347 6.5401 7.292L7.4451 7.203ZM4.5771 11.75C4.47892 13.64 4.57611 15.535 4.8671 17.405C4.91864 17.7356 5.0785 18.0396 5.32157 18.2695C5.56465 18.4994 5.87718 18.642 6.2101 18.675L7.4031 18.793C10.4601 19.095 13.5401 19.095 16.5971 18.793L17.7901 18.675C18.123 18.642 18.4355 18.4994 18.6786 18.2695C18.9217 18.0396 19.0816 17.7356 19.1331 17.405C19.4251 15.533 19.5211 13.638 19.4231 11.75H4.5771Z"
                                fill="#283326" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Hamburger Button -->
                {{-- <button id="mobile-menu-button" class="md:hidden text-ivory focus:outline-none">
                    <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button> --}}

                <!-- Mobile Hamburger Button -->
                <button id="mobile-menu-button" class="md:hidden text-ivory focus:outline-none">
                    <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-evergreen border-t border-ivory/20">
                <ul class="flex flex-col py-4 text-ivory font-league-gothic text-xl tracking-wider">
                    <li class="border-b border-ivory/10">
                        <a href="{{ route('home') }}"
                            class="block py-3 px-4 hover:text-white hover:bg-ivory/10 transition-colors {{ request()->routeIs('home') ? 'border-l-4 border-ivory' : '' }}">
                            HOME
                        </a>
                    </li>
                    <li class="border-b border-ivory/10">
                        <a href="{{ route('services') }}"
                            class="block py-3 px-4 hover:text-white hover:bg-ivory/10 transition-colors {{ request()->routeIs('services') ? 'border-l-4 border-ivory' : '' }}">
                            SERVICES
                        </a>
                    </li>
                    <li class="border-b border-ivory/10">
                        <a href="{{ route('gallery') }}"
                            class="block py-3 px-4 hover:text-white hover:bg-ivory/10 transition-colors {{ request()->routeIs('gallery') ? 'border-l-4 border-ivory' : '' }}">
                            GALLERY
                        </a>
                    </li>
                    <li class="border-b border-ivory/10">
                        <a href="{{ route('contact') }}"
                            class="block py-3 px-4 hover:text-white hover:bg-ivory/10 transition-colors {{ request()->routeIs('contact') ? 'border-l-4 border-ivory' : '' }}">
                            CONTACT US
                        </a>
                    </li>
                    <li class="pt-2">
                        <button
                            class="book-now-btn w-full bg-ivory text-evergreen font-league-gothic text-xl tracking-wider p-2 flex items-center justify-center gap-3 hover:bg-white transition-colors">
                            BOOK NOW
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7.0001 3.25C7.19901 3.25 7.38978 3.32902 7.53043 3.46967C7.67108 3.61032 7.7501 3.80109 7.7501 4V5.668C10.5779 5.41664 13.4223 5.41664 16.2501 5.668V4C16.2501 3.80109 16.3291 3.61032 16.4698 3.46967C16.6104 3.32902 16.8012 3.25 17.0001 3.25C17.199 3.25 17.3898 3.32902 17.5304 3.46967C17.6711 3.61032 17.7501 3.80109 17.7501 4V5.816C18.4796 5.91945 19.1553 6.25882 19.6739 6.78228C20.1924 7.30573 20.5255 7.98452 20.6221 8.715L20.7091 9.368C21.0731 12.114 21.0411 14.898 20.6151 17.636C20.5124 18.2951 20.1936 18.9015 19.7089 19.3598C19.2242 19.8181 18.6009 20.1024 17.9371 20.168L16.7441 20.286C13.5891 20.5967 10.4111 20.5967 7.2561 20.286L6.0631 20.168C5.39925 20.1024 4.77601 19.8181 4.29128 19.3598C3.80655 18.9015 3.4878 18.2951 3.3851 17.636C2.95901 14.8986 2.92736 12.1143 3.2911 9.368L3.3781 8.715C3.47472 7.98452 3.80775 7.30573 4.32633 6.78228C4.84491 6.25882 5.52056 5.91945 6.2501 5.816V4C6.2501 3.80109 6.32912 3.61032 6.46977 3.46967C6.61042 3.32902 6.80119 3.25 7.0001 3.25ZM7.4451 7.203C10.4751 6.904 13.5251 6.904 16.5551 7.203L17.4601 7.293C18.3271 7.378 19.0201 8.049 19.1351 8.912L19.2221 9.565C19.2521 9.793 19.2794 10.0213 19.3041 10.25H4.6961C4.72077 10.0213 4.7481 9.793 4.7781 9.565L4.8651 8.912C4.9206 8.49261 5.11629 8.10431 5.42038 7.8102C5.72447 7.5161 6.11909 7.33347 6.5401 7.292L7.4451 7.203ZM4.5771 11.75C4.47892 13.64 4.57611 15.535 4.8671 17.405C4.91864 17.7356 5.0785 18.0396 5.32157 18.2695C5.56465 18.4994 5.87718 18.642 6.2101 18.675L7.4031 18.793C10.4601 19.095 13.5401 19.095 16.5971 18.793L17.7901 18.675C18.123 18.642 18.4355 18.4994 18.6786 18.2695C18.9217 18.0396 19.0816 17.7356 19.1331 17.405C19.4251 15.533 19.5211 13.638 19.4231 11.75H4.5771Z"
                                    fill="#283326" />
                            </svg>
                        </button>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
