@extends('frontend.layouts.app')

@push('styles')
@endpush

@section('content')
    <main>
        <!-- Hero Start -->
        <section
            class="relative bg-linear-to-r from-ivory/60 to-[#1B4629]/60 opacity-[52px] pb-10 min-h-fit 3xl:max-h-[60vh] flex pt-36 overflow-hidden">

            <!-- Single Background Image from Database -->
            <div class="absolute inset-0 w-full h-full">
                <img src="{{ asset($slider->image) }}" alt="Nexo Barbershop Background"
                    class="w-full h-full object-cover opacity-90">
            </div>

            <!-- Gradient Overlay (optional - to maintain the original color effect) -->
            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-ivory/40 to-[#1B4629]/40 pointer-events-none">
            </div>

            <div class="container mx-auto px-4 md:px-8 lg:px-12 lg:pl-18 relative z-10">
                <!-- Content -->
                <div class="mb-10 md:mb-32 xl:mb-80">
                    <div class="max-w-xl mx-auto lg:mx-0 text-center lg:text-left flex flex-col items-center lg:items-start">
                        <h1 class="font-league-gothic text-6xl md:text-7xl text-evergreen leading-tight mb-6">
                            {{ $slider->title }}
                        </h1>

                        <p class="font-league-gothic text-evergreen text-2xl font-normal mb-6">
                            CALL US: {{ $settings->valueOf('phone') }}
                        </p>

                        <button
                            class="book-now-btn bg-evergreen cursor-pointer text-ivory font-league-gothic text-xl tracking-wider px-6 py-3 flex items-center justify-center lg:justify-start gap-3 hover:bg-opacity-90 transition-all">
                            {{ $slider->button_text }}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7.0001 3.25C7.19901 3.25 7.38978 3.32902 7.53043 3.46967C7.67108 3.61032 7.7501 3.80109 7.7501 4V5.668C10.5779 5.41664 13.4223 5.41664 16.2501 5.668V4C16.2501 3.80109 16.3291 3.61032 16.4698 3.46967C16.6104 3.32902 16.8012 3.25 17.0001 3.25C17.199 3.25 17.3898 3.32902 17.5304 3.46967C17.6711 3.61032 17.7501 3.80109 17.7501 4V5.816C18.4796 5.91945 19.1553 6.25882 19.6739 6.78228C20.1924 7.30573 20.5255 7.98452 20.6221 8.715L20.7091 9.368C21.0731 12.114 21.0411 14.898 20.6151 17.636C20.5124 18.2951 20.1936 18.9015 20.7089 19.3598C19.2242 19.8181 18.6009 20.1024 17.9371 20.168L16.7441 20.286C13.5891 20.5967 10.4111 20.5967 7.2561 20.286L6.0631 20.168C5.39925 20.1024 4.77601 19.8181 4.29128 19.3598C3.80655 18.9015 3.4878 18.2951 3.3851 17.636C2.95901 14.8986 2.92736 12.1143 3.2911 9.368L3.3781 8.715C3.47472 7.98452 3.80775 7.30573 4.32633 6.78228C4.84491 6.25882 5.52056 5.91945 6.2501 5.816V4C6.2501 3.80109 6.32912 3.61032 6.46977 3.46967C6.61042 3.32902 6.80119 3.25 7.0001 3.25ZM7.4451 7.203C10.4751 6.904 13.5251 6.904 16.5551 7.203L17.4601 7.293C18.3271 7.378 19.0201 8.049 19.1351 8.912L19.2221 9.565C19.2521 9.793 19.2794 10.0213 19.3041 10.25H4.6961C4.72077 10.0213 4.7481 9.793 4.7781 9.565L4.8651 8.912C4.9206 8.49261 5.11629 8.10431 5.42038 7.8102C5.72447 7.5161 6.11909 7.33347 6.5401 7.292L7.4451 7.203ZM4.5771 11.75C4.47892 13.64 4.57611 15.535 4.8671 17.405C4.91864 17.7356 5.0785 18.0396 5.32157 18.2695C5.56465 18.4994 5.87718 18.642 6.2101 18.675L7.4031 18.793C10.4601 19.095 13.5401 19.095 16.5971 18.793L17.7901 18.675C18.123 18.642 18.4355 18.4994 18.6786 18.2695C18.9217 18.0396 19.0816 17.7356 19.1331 17.405C19.4251 15.533 19.5211 13.638 19.4231 11.75H4.5771Z"
                                    fill="#F8EDD2" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Large NEXO Text Watermark -->
                {{-- <div class="absolute left-18 bottom-0 w-1/2 pointer-events-none hidden md:block">
                    <img src="{{ asset('frontend_assets/assets/images/NexoLogoHero.svg') }}" alt="Nexo Logo" />
                </div> --}}
            </div>

            <!-- Large X Scissors Logo -->
            {{-- <div class="absolute right-1/5 pointer-events-none hidden xl:block">
                <img src="{{ asset('frontend_assets/assets/images/xlogo.svg') }}" alt="Scissors Logo" />
            </div> --}}
        </section>

        <!-- About Start -->
        <section class="relative py-16 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0">
                <img src="{{ asset('frontend_assets/assets/images/bg-about.png') }}" alt="Background pattern"
                    class="w-full h-full object-cover">
            </div>

            <div class="container mx-auto px-4 md:px-8 lg:px-12 relative z-10">
                <!-- Row 1: Barber Icon + Main Content + Book Now Button -->
                <div class="flex flex-col lg:flex-row lg:items-center gap-8 mb-6">
                    <!-- Content -->
                    <div class="grow">
                        <!-- About Us Header -->
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <svg width="42" height="71" viewBox="0 0 42 71" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.773 4.4375H25.2727C28.7461 4.4375 31.5516 7.25531 31.5516 10.6944V10.7388H31.6406C34.6465 10.7388 37.0734 13.1572 37.0734 16.1525C37.0734 19.1478 34.6465 21.5663 31.6406 21.5663H24.293L13.6055 26.7138V21.5663H13.4719C10.466 21.5663 8.03906 19.1478 8.03906 16.1525C8.03906 13.1572 10.466 10.7388 13.4719 10.7388H13.4941V10.6944C13.4941 7.23313 16.2996 4.4375 19.773 4.4375ZM31.6629 30.4413L13.6055 39.1609V43.9534L31.6629 35.2559V30.4413ZM31.6406 55.7794H24.7984L31.6629 52.4734V47.6809L14.8501 55.7794H13.4719C10.466 55.7794 8.03906 58.1978 8.03906 61.1931C8.03906 64.1884 10.466 66.6069 13.4719 66.6069H31.6406C34.6465 66.6069 37.0734 64.1884 37.0734 61.1931C37.0734 58.1978 34.6465 55.7794 31.6406 55.7794Z"
                                    fill="#F8EDD2" />
                                <path
                                    d="M13.6055 30.53L31.6629 21.8325V26.625L13.6055 35.3225V30.53ZM13.6055 47.7697L31.6629 39.0722V43.8647L13.6055 52.5622V47.7697Z"
                                    fill="#F8EDD2" />
                            </svg>
                            <div class="h-px bg-ivory grow max-w-full"></div>
                            <h2 class="font-league-gothic text-ivory text-[40px] tracking-widest">{{ $about->title }}</h2>
                        </div>

                        <!-- Main Content Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-12">
                            <!-- Left Column - Image -->
                            <div class="relative col-span-1 lg:col-span-2 mt-6 mx-auto lg:mx-0">
                                <div class="w-58 h-76 overflow-hidden bg-[#F8EDD2C7] rotate-12">
                                    <!-- <img src="./assets/images/bg-left.png" alt="Barber at work" class="w-full h-full object-cover"> -->
                                </div>
                                <div class="w-58 h-76 overflow-hidden z-20 absolute top-0">
                                    <img src="{{ asset($about->image1) }}" alt="Barber at work"
                                        class="w-full h-full object-cover">
                                </div>

                                <!-- Arrow Button -->
                                <button onclick="window.location.href='{{ $about->button_link }}'"
                                    class="absolute mt-6 md:mt-0 bottom-0 md:bottom-8 left-12 w-16 h-16 rounded-full bg-ivory flex items-center justify-center hover:bg-white transition-colors hover:scale-110 z-50">
                                    <svg width="39" height="38" viewBox="0 0 39 38" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21.8888 13.5058C21.7691 13.397 21.6731 13.2659 21.6065 13.1203C21.5398 12.9746 21.504 12.8174 21.5011 12.6579C21.4982 12.4985 21.5284 12.3401 21.5896 12.1922C21.6509 12.0444 21.7422 11.91 21.8579 11.7973C21.9736 11.6845 22.1115 11.5956 22.2633 11.5359C22.415 11.4762 22.5776 11.4468 22.7412 11.4496C22.9048 11.4525 23.0662 11.4874 23.2157 11.5523C23.3652 11.6172 23.4998 11.7107 23.6113 11.8274L30.1113 18.1608C30.3396 18.3834 30.4678 18.6852 30.4678 18.9999C30.4678 19.3146 30.3396 19.6164 30.1113 19.8391L23.6113 26.1724C23.4998 26.2891 23.3652 26.3827 23.2157 26.4476C23.0662 26.5125 22.9048 26.5474 22.7412 26.5502C22.5776 26.553 22.415 26.5237 22.2633 26.4639C22.1115 26.4042 21.9736 26.3153 21.8579 26.2026C21.7422 26.0898 21.6509 25.9555 21.5896 25.8076C21.5284 25.6597 21.4982 25.5014 21.5011 25.3419C21.504 25.1825 21.5398 25.0252 21.6065 24.8796C21.6731 24.7339 21.7691 24.6028 21.8888 24.4941L26.3088 20.1874L10.5626 20.1874C10.2394 20.1874 9.92937 20.0623 9.70081 19.8396C9.47225 19.6169 9.34385 19.3149 9.34385 18.9999C9.34385 18.685 9.47225 18.3829 9.70081 18.1602C9.92937 17.9375 10.2394 17.8124 10.5626 17.8124L26.3088 17.8124L21.8888 13.5058Z"
                                            fill="#283327" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Center Column - Nexo Logo -->
                            <!-- <img src="./assets/images/about-nexo.png" -->
                            <img src="{{ asset('frontend_assets/assets/images/NexoAbout.svg') }}"
                                class="col-span-1 lg:col-span-8 w-[95%] mt-40 md:mt-4 mx-auto px-8" />
                            <!-- <img src="./assets/images/about-nexo.png"
                                                                        class="col-span-1 lg:col-span-8 w-full mt-20 md:mt-0" /> -->


                            <!-- Right Column - Text Content -->
                            <div class="text-ivory col-span-1 lg:col-span-2 mt-10 text-center lg:text-left">
                                <p class="font-league-gothic text-xl font-normal leading-relaxed uppercase">
                                    {{ $about->description }}
                                </p>
                                <button
                                    class="book-now-btn mt-6 bg-ivory text-evergreen font-league-gothic text-xl tracking-wider px-6 py-2 flex items-center gap-2 hover:bg-white transition-colors cursor-pointer mx-auto lg:mx-0">
                                    BOOK NOW
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7.0001 3.25C7.19901 3.25 7.38978 3.32902 7.53043 3.46967C7.67108 3.61032 7.7501 3.80109 7.7501 4V5.668C10.5779 5.41664 13.4223 5.41664 16.2501 5.668V4C16.2501 3.80109 16.3291 3.61032 16.4698 3.46967C16.6104 3.32902 16.8012 3.25 17.0001 3.25C17.199 3.25 17.3898 3.32902 17.5304 3.46967C17.6711 3.61032 17.7501 3.80109 17.7501 4V5.816C18.4796 5.91945 19.1553 6.25882 19.6739 6.78228C20.1924 7.30573 20.5255 7.98452 20.6221 8.715L20.7091 9.368C21.0731 12.114 21.0411 14.898 20.6151 17.636C20.5124 18.2951 20.1936 18.9015 19.7089 19.3598C19.2242 19.8181 18.6009 20.1024 17.9371 20.168L16.7441 20.286C13.5891 20.5967 10.4111 20.5967 7.2561 20.286L6.0631 20.168C5.39925 20.1024 4.77601 19.8181 4.29128 19.3598C3.80655 18.9015 3.4878 18.2951 3.3851 17.636C2.95901 14.8986 2.92736 12.1143 3.2911 9.368L3.3781 8.715C3.47472 7.98452 3.80775 7.30573 4.32633 6.78228C4.84491 6.25882 5.52056 5.91945 6.2501 5.816V4C6.2501 3.80109 6.32912 3.61032 6.46977 3.46967C6.61042 3.32902 6.80119 3.25 7.0001 3.25ZM7.4451 7.203C10.4751 6.904 13.5251 6.904 16.5551 7.203L17.4601 7.293C18.3271 7.378 19.0201 8.049 19.1351 8.912L19.2221 9.565C19.2521 9.793 19.2794 10.0213 19.3041 10.25H4.6961C4.72077 10.0213 4.7481 9.793 4.7781 9.565L4.8651 8.912C4.9206 8.49261 5.11629 8.10431 5.42038 7.8102C5.72447 7.5161 6.11909 7.33347 6.5401 7.292L7.4451 7.203ZM4.5771 11.75C4.47892 13.64 4.57611 15.535 4.8671 17.405C4.91864 17.7356 5.0785 18.0396 5.32157 18.2695C5.56465 18.4994 5.87718 18.642 6.2101 18.675L7.4031 18.793C10.4601 19.095 13.5401 19.095 16.5971 18.793L17.7901 18.675C18.123 18.642 18.4355 18.4994 18.6786 18.2695C18.9217 18.0396 19.0816 17.7356 19.1331 17.405C19.4251 15.533 19.5211 13.638 19.4231 11.75H4.5771Z"
                                            fill="currentColor" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Services Section + X Logo -->
                <div class="flex flex-col justify-between pt-12">
                    <!-- Services Box -->
                    <div class="flex flex-col justify-between pt-12">
                        <!-- Services Box -->
                        <div class="shrink-0 flex justify-between">
                            <div class="px-8 py-6 flex flex-col md:flex-row items-end mb-14 gap-8">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-20 h-20 p-2 border border-ivory flex items-center justify-center">
                                        <div class="w-18 h-18 p-2 border border-ivory flex items-center justify-center">
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
                                    <h3
                                        class="font-league-gothic text-ivory text-2xl md:text-3xl lg:text-4xl tracking-wider">
                                        SERVICES :</h3>
                                </div>
                                <ul
                                    class="space-y-2 text-ivory font-league-gothic text-2xl grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($services->take(4) as $service)
                                        <li class="flex items-center gap-2">
                                            <span class="text-2xl">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.0002 7.99992C15.0017 8.205 14.9394 8.40548 14.8218 8.5735C14.7042 8.74151 14.5371 8.86871 14.3439 8.93742L10.3789 10.3793L8.93766 14.3437C8.86662 14.535 8.73872 14.7001 8.57113 14.8167C8.40354 14.9332 8.20429 14.9957 8.00016 14.9957C7.79602 14.9957 7.59678 14.9332 7.42919 14.8167C7.2616 14.7001 7.1337 14.535 7.06266 14.3437L5.62141 10.3787L1.65641 8.93742C1.46503 8.86638 1.29998 8.73847 1.18342 8.57089C1.06687 8.4033 1.00439 8.20405 1.00439 7.99992C1.00439 7.79578 1.06687 7.59653 1.18342 7.42894C1.29998 7.26136 1.46503 7.13345 1.65641 7.06242L5.62141 5.62117L7.06266 1.65617C7.1337 1.46479 7.2616 1.29974 7.42919 1.18318C7.59678 1.06662 7.79602 1.00415 8.00016 1.00415C8.20429 1.00415 8.40354 1.06662 8.57113 1.18318C8.73872 1.29974 8.86662 1.46479 8.93766 1.65617L10.3795 5.62117L14.3439 7.06242C14.5371 7.13112 14.7042 7.25832 14.8218 7.42633C14.9394 7.59435 15.0017 7.79483 15.0002 7.99992Z"
                                                        fill="#F8EDD2" />
                                                </svg>
                                            </span> {{ strtoupper($service->name) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="hidden lg:block">
                                <img src="{{ asset('frontend_assets/assets/images/Large-about-x.svg') }}" alt="X Logo">
                            </div>
                        </div>

                        <!-- Bottom Info Bar -->
                        <div class="grow z-20 md:-mt-12">
                            <div
                                class="bg-ivory py-4 px-8 lg:px-12 flex flex-col md:flex-row items-center justify-between gap-4 text-evergreen font-league-gothic text-3xl">
                                <div class="flex items-center gap-2">
                                    <span>{{ $settings->valueOf('web_address') }}</span>
                                </div>
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M31.9999 16C32.0036 16.469 31.861 16.9276 31.5921 17.3118C31.3232 17.6961 30.9413 17.987 30.4995 18.1442L21.4339 21.4419L18.1386 30.5088C17.9762 30.9465 17.6838 31.324 17.3006 31.5905C16.9174 31.8571 16.4619 32 15.9951 32C15.5284 32 15.0728 31.8571 14.6897 31.5905C14.3065 31.324 14.014 30.9465 13.8516 30.5088L10.5563 21.4404L1.49077 18.1442C1.0532 17.9817 0.675825 17.6892 0.409333 17.3059C0.142841 16.9226 0 16.4669 0 16C0 15.5331 0.142841 15.0774 0.409333 14.6941C0.675825 14.3108 1.0532 14.0183 1.49077 13.8558L10.5563 10.5596L13.8516 1.49122C14.014 1.05352 14.3065 0.676031 14.6897 0.409458C15.0728 0.142884 15.5284 0 15.9951 0C16.4619 0 16.9174 0.142884 17.3006 0.409458C17.6838 0.676031 17.9762 1.05352 18.1386 1.49122L21.4353 10.5596L30.4995 13.8558C30.9413 14.013 31.3232 14.3039 31.5921 14.6882C31.861 15.0724 32.0036 15.531 31.9999 16Z"
                                        fill="#283327" />
                                </svg>
                                <div class="flex items-center gap-2">
                                    <span class="text-crimson">OPEN
                                        {{ $about->tab1_title }}AM-{{ $about->tab1_content }}PM</span>
                                </div>
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M31.9999 16C32.0036 16.469 31.861 16.9276 31.5921 17.3118C31.3232 17.6961 30.9413 17.987 30.4995 18.1442L21.4339 21.4419L18.1386 30.5088C17.9762 30.9465 17.6838 31.324 17.3006 31.5905C16.9174 31.8571 16.4619 32 15.9951 32C15.5284 32 15.0728 31.8571 14.6897 31.5905C14.3065 31.324 14.014 30.9465 13.8516 30.5088L10.5563 21.4404L1.49077 18.1442C1.0532 17.9817 0.675825 17.6892 0.409333 17.3059C0.142841 16.9226 0 16.4669 0 16C0 15.5331 0.142841 15.0774 0.409333 14.6941C0.675825 14.3108 1.0532 14.0183 1.49077 13.8558L10.5563 10.5596L13.8516 1.49122C14.014 1.05352 14.3065 0.676031 14.6897 0.409458C15.0728 0.142884 15.5284 0 15.9951 0C16.4619 0 16.9174 0.142884 17.3006 0.409458C17.6838 0.676031 17.9762 1.05352 18.1386 1.49122L21.4353 10.5596L30.4995 13.8558C30.9413 14.013 31.3232 14.3039 31.5921 14.6882C31.861 15.0724 32.0036 15.531 31.9999 16Z"
                                        fill="#283327" />
                                </svg>
                                <div class="flex items-center gap-2">
                                    <span>CALL:{{ $settings->valueOf('phone') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Info Bar -->
                    <div class="grow z-20 md:-mt-12">
                        <div
                            class="bg-ivory py-4 px-8 lg:px-12 flex flex-col md:flex-row items-center justify-between gap-4 text-evergreen font-league-gothic text-3xl">
                            <div class="flex items-center gap-2">
                                <span>{{ $settings->valueOf('web_address') }}</span>
                            </div>
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M31.9999 16C32.0036 16.469 31.861 16.9276 31.5921 17.3118C31.3232 17.6961 30.9413 17.987 30.4995 18.1442L21.4339 21.4419L18.1386 30.5088C17.9762 30.9465 17.6838 31.324 17.3006 31.5905C16.9174 31.8571 16.4619 32 15.9951 32C15.5284 32 15.0728 31.8571 14.6897 31.5905C14.3065 31.324 14.014 30.9465 13.8516 30.5088L10.5563 21.4404L1.49077 18.1442C1.0532 17.9817 0.675825 17.6892 0.409333 17.3059C0.142841 16.9226 0 16.4669 0 16C0 15.5331 0.142841 15.0774 0.409333 14.6941C0.675825 14.3108 1.0532 14.0183 1.49077 13.8558L10.5563 10.5596L13.8516 1.49122C14.014 1.05352 14.3065 0.676031 14.6897 0.409458C15.0728 0.142884 15.5284 0 15.9951 0C16.4619 0 16.9174 0.142884 17.3006 0.409458C17.6838 0.676031 17.9762 1.05352 18.1386 1.49122L21.4353 10.5596L30.4995 13.8558C30.9413 14.013 31.3232 14.3039 31.5921 14.6882C31.861 15.0724 32.0036 15.531 31.9999 16Z"
                                    fill="#283327" />
                            </svg>
                            <div class="flex items-center gap-2">
                                <span class="text-crimson">OPEN
                                    {{ $about->tab1_title }}AM-{{ $about->tab1_content }}PM</span>
                            </div>
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M31.9999 16C32.0036 16.469 31.861 16.9276 31.5921 17.3118C31.3232 17.6961 30.9413 17.987 30.4995 18.1442L21.4339 21.4419L18.1386 30.5088C17.9762 30.9465 17.6838 31.324 17.3006 31.5905C16.9174 31.8571 16.4619 32 15.9951 32C15.5284 32 15.0728 31.8571 14.6897 31.5905C14.3065 31.324 14.014 30.9465 13.8516 30.5088L10.5563 21.4404L1.49077 18.1442C1.0532 17.9817 0.675825 17.6892 0.409333 17.3059C0.142841 16.9226 0 16.4669 0 16C0 15.5331 0.142841 15.0774 0.409333 14.6941C0.675825 14.3108 1.0532 14.0183 1.49077 13.8558L10.5563 10.5596L13.8516 1.49122C14.014 1.05352 14.3065 0.676031 14.6897 0.409458C15.0728 0.142884 15.5284 0 15.9951 0C16.4619 0 16.9174 0.142884 17.3006 0.409458C17.6838 0.676031 17.9762 1.05352 18.1386 1.49122L21.4353 10.5596L30.4995 13.8558C30.9413 14.013 31.3232 14.3039 31.5921 14.6882C31.861 15.0724 32.0036 15.531 31.9999 16Z"
                                    fill="#283327" />
                            </svg>
                            <div class="flex items-center gap-2">
                                <span>CALL:{{ $settings->valueOf('phone') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery/Services Grid Section -->
        <section class="relative bg-black py-0 overflow-hidden">
            <!-- 2x4 Grid of Images -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-11 grid-rows-2 max-h-full lg:max-h-120">
                @php
                    $colSpans = [
                        0 => 'col-span-1 lg:col-span-3', // Row 1 - Item 1
                        1 => 'col-span-1 lg:col-span-3', // Row 1 - Item 2
                        2 => 'col-span-1 lg:col-span-3', // Row 1 - Item 3
                        3 => 'col-span-1 lg:col-span-2', // Row 1 - Item 4
                        4 => 'col-span-1 lg:col-span-2', // Row 2 - Item 1
                        5 => 'col-span-1 lg:col-span-5', // Row 2 - Item 2 (Large Center)
                        6 => 'col-span-1 lg:col-span-2', // Row 2 - Item 3
                        7 => 'col-span-1 lg:col-span-2', // Row 2 - Item 4
                    ];
                @endphp

                @foreach ($works as $index => $work)
                    <div
                        class="relative overflow-hidden group max-h-60 {{ $colSpans[$index] ?? 'col-span-1 lg:col-span-2' }}">
                        <img src="{{ asset($work->image) }}" alt="{{ $work->title }}"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/60 transition-all duration-300">
                        </div>
                        <div class="absolute inset-0 flex items-end justify-center pb-4">
                            <h3
                                class="text-ivory font-league-gothic {{ $index == 5 ? 'text-2xl lg:text-3xl' : 'text-2xl' }} tracking-wider text-center px-4">
                                {{ $work->title }}
                            </h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        <!-- Service Prices Section -->
        <section class="relative py-16 lg:py-24 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0">
                <img src="{{ asset('frontend_assets/assets/images/bg-left.png') }}" alt="Background pattern"
                    class="w-full h-full object-cover">
            </div>

            <div class="container mx-auto px-4 md:px-8 lg:px-12 xl:px-32 relative z-10">
                <div class="items-start">
                    <!-- Left Column - Service Prices -->
                    <div class="font-league-gothic">
                        <!-- Header -->
                        <div>
                            <h2 class="text-evergreen text-5xl lg:text-6xl xl:text-8xl">
                                {{ $sections['services_section']->title ?? 'Service prices' }}
                            </h2>
                            <p class="text-[#283327C2] text-2xl md:text-3xl lg:text-4xl">
                                {{ $sections['services_section']->description ?? 'Luxury grooming services, thoughtfully priced.' }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <div class="w-full h-0.75 bg-[#28332729]"></div>
                            <svg width="42" height="71" viewBox="0 0 42 71" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.773 4.4375H25.2727C28.7461 4.4375 31.5516 7.25531 31.5516 10.6944V10.7388H31.6406C34.6465 10.7388 37.0734 13.1572 37.0734 16.1525C37.0734 19.1478 34.6465 21.5663 31.6406 21.5663H24.293L13.6055 26.7138V21.5663H13.4719C10.466 21.5663 8.03906 19.1478 8.03906 16.1525C8.03906 13.1572 10.466 10.7388 13.4719 10.7388H13.4941V10.6944C13.4941 7.23313 16.2996 4.4375 19.773 4.4375ZM31.6629 30.4413L13.6055 39.1609V43.9534L31.6629 35.2559V30.4413ZM31.6406 55.7794H24.7984L31.6629 52.4734V47.6809L14.8501 55.7794H13.4719C10.466 55.7794 8.03906 58.1978 8.03906 61.1931C8.03906 64.1884 10.466 66.6069 13.4719 66.6069H31.6406C34.6465 66.6069 37.0734 64.1884 37.0734 61.1931C37.0734 58.1978 34.6465 55.7794 31.6406 55.7794Z"
                                    fill="#283327" />
                                <path
                                    d="M13.6055 30.53L31.6629 21.8325V26.625L13.6055 35.3225V30.53ZM13.6055 47.7697L31.6629 39.0722V43.8647L13.6055 52.5622V47.7697Z"
                                    fill="#283327" />
                            </svg>
                        </div>

                        <!-- Service Prices List -->
                        <div class="space-y-4 mb-8 font-league-gothic text-2xl">
                            <h3 class="text-black text-2xl md:text-3xl lg:text-4xl mb-6 uppercase">
                                Service prices
                            </h3>

                            <!-- Price Items - Dynamic -->
                            @foreach ($services as $service)
                                <div class="flex justify-between items-center pb-1">
                                    <span class="text-[#444444]">{{ $service->name }}</span>
                                    <span class="text-crimson text-3xl">{{ $service->description }}$</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- All Services Button -->
                        <a href="{{ route('services') }}" class="relative inline-block">
                            <button
                                class="bg-evergreen hover:bg-evergreen/80 text-ivory font-league-gothic text-lg md:text-2xl lg:text-3xl tracking-wider px-8 py-4 flex items-center gap-3 hover:bg-opacity-90 transition-all group active:bg-evergreen/70">
                                All Services
                                <svg class="group-hover:-rotate-90 transition-transform duration-200" width="22"
                                    height="14" viewBox="0 0 22 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.739746 0.672729L10.7397 11.6727L20.7397 0.672729" stroke="white"
                                        stroke-width="2" />
                                </svg>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Higher Standard Section -->
        <section class="relative bg-evergreen py-10 overflow-hidden px-4 md:px-8">
            <!-- Background Pattern -->
            <div class="absolute inset-0">
                <img src="{{ asset('frontend_assets/assets/images/bg-about.png') }}" alt="Background pattern"
                    class="w-full h-full object-cover">
            </div>
            <div class="container mx-auto relative z-10 border-2 border-ivory/30 pointer-events-none">
                <div class="grid lg:grid-cols-2 items-center max-h-full lg:max-h-187.5">
                    <!-- Left Column - Image -->
                    <div class="relative h-full">
                        <div class="relative h-full">
                            <img src="{{ asset($sections['contact_page']->image) }}" alt="Professional Grooming"
                                class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Right Column - Content -->
                    <div class="text-ivory space-y-8 px-4 py-8">
                        <!-- Logo and Heading -->
                        <div class="text-center mx-auto">
                            <img src="{{ asset('frontend_assets/assets/images/nexo-higher-standard.svg') }}"
                                alt="X Scissors Logo" class="w-68 h-48 opacity-80 mx-auto">
                            <h3
                                class="font-league-gothic text-2xl lg:text-4xl uppercase tracking-wider text-center mx-auto">
                                A Higher Standard of Grooming
                            </h3>
                        </div>

                        <!-- Features Grid -->
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4 gap-4 max-h-full">
                            <!-- Premium Tools -->
                            <div
                                class="flex flex-col items-center text-center p-4 border border-white hover:border-ivory transition-colors bg-[#283327] min-h-fit">
                                <div class="w-20 h-20 rounded-full bg-ivory flex items-center justify-center mb-3">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 15C12.7614 15 15 12.7614 15 10C15 7.23858 12.7614 5 10 5C7.23858 5 5 7.23858 5 10C5 12.7614 7.23858 15 10 15Z"
                                            stroke="#283327" stroke-width="3.33333" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M13.5332 13.5333L19.9999 20" stroke="#283327" stroke-width="3.33333"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M33.3332 6.66663L13.5332 26.4666" stroke="#283327" stroke-width="3.33333"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M10 35C12.7614 35 15 32.7614 15 30C15 27.2386 12.7614 25 10 25C7.23858 25 5 27.2386 5 30C5 32.7614 7.23858 35 10 35Z"
                                            stroke="#283327" stroke-width="3.33333" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M24.6665 24.6666L33.3332 33.3333" stroke="#283327" stroke-width="3.33333"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <h4 class="font-league-gothic text-2xl md:text-3xl uppercase mt-2">Premium Tools</h4>
                            </div>

                            <!-- Clean & Modern Space -->
                            <div
                                class="flex flex-col items-center text-center p-4 border border-white hover:border-ivory transition-colors bg-[#283327]">
                                <div class="w-20 h-20 rounded-full bg-ivory flex items-center justify-center mb-3">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M33.3332 16.6667C33.3332 24.9884 24.1015 33.655 21.0015 36.3317C20.7127 36.5489 20.3612 36.6663 19.9998 36.6663C19.6385 36.6663 19.287 36.5489 18.9982 36.3317C15.8982 33.655 6.6665 24.9884 6.6665 16.6667C6.6665 13.1305 8.07126 9.7391 10.5717 7.23862C13.0722 4.73813 16.4636 3.33337 19.9998 3.33337C23.5361 3.33337 26.9274 4.73813 29.4279 7.23862C31.9284 9.7391 33.3332 13.1305 33.3332 16.6667Z"
                                            stroke="#283327" stroke-width="3.33333" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M20 21.6666C22.7614 21.6666 25 19.428 25 16.6666C25 13.9052 22.7614 11.6666 20 11.6666C17.2386 11.6666 15 13.9052 15 16.6666C15 19.428 17.2386 21.6666 20 21.6666Z"
                                            stroke="#283327" stroke-width="3.33333" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <h4 class="font-league-gothic text-2xl md:text-3xl uppercase mt-2">Clean & Modern Space
                                </h4>
                            </div>

                            <!-- Friendly Atmosphere -->
                            <div
                                class="flex flex-col items-center text-center p-4 border border-white hover:border-ivory transition-colors bg-[#283327]">
                                <div class="w-20 h-20 rounded-full bg-ivory flex items-center justify-center mb-3">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M26.6668 35V31.6667C26.6668 29.8986 25.9645 28.2029 24.7142 26.9526C23.464 25.7024 21.7683 25 20.0002 25H10.0002C8.23205 25 6.53636 25.7024 5.28612 26.9526C4.03588 28.2029 3.3335 29.8986 3.3335 31.6667V35"
                                            stroke="#283327" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M15.0002 18.3333C18.6821 18.3333 21.6668 15.3486 21.6668 11.6667C21.6668 7.98477 18.6821 5 15.0002 5C11.3183 5 8.3335 7.98477 8.3335 11.6667C8.3335 15.3486 11.3183 18.3333 15.0002 18.3333Z"
                                            stroke="#283327" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M36.6665 35V31.6667C36.6654 30.1896 36.1738 28.7546 35.2688 27.5872C34.3638 26.4198 33.0967 25.586 31.6665 25.2167"
                                            stroke="#283327" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M26.6665 5.21667C28.1005 5.58384 29.3716 6.41784 30.2792 7.58719C31.1869 8.75654 31.6796 10.1947 31.6796 11.675C31.6796 13.1553 31.1869 14.5935 30.2792 15.7628C29.3716 16.9322 28.1005 17.7662 26.6665 18.1333"
                                            stroke="#283327" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <h4 class="font-league-gothic text-2xl md:text-3xl uppercase mt-2">Friendly Atmosphere
                                </h4>
                            </div>

                            <!-- Online Booking -->
                            <div
                                class="flex flex-col items-center text-center p-4 border border-white hover:border-ivory transition-colors bg-[#283327]">
                                <div class="w-20 h-20 rounded-full bg-ivory flex items-center justify-center mb-3">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.3335 3.33337V10" stroke="#283327" stroke-width="3.33333"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M26.6665 3.33337V10" stroke="#283327" stroke-width="3.33333"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M31.6667 6.66663H8.33333C6.49238 6.66663 5 8.15901 5 9.99996V33.3333C5 35.1742 6.49238 36.6666 8.33333 36.6666H31.6667C33.5076 36.6666 35 35.1742 35 33.3333V9.99996C35 8.15901 33.5076 6.66663 31.6667 6.66663Z"
                                            stroke="#283327" stroke-width="3.33333" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M5 16.6666H35" stroke="#283327" stroke-width="3.33333"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                </div>
                                <h4 class="font-league-gothic text-2xl md:text-3xl uppercase mt-2">Online Booking</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Us Header -->
            <div class="container mx-auto px-4 md:px-8 lg:px-12 relative z-10 pt-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-6 w-full">
                        <h2 class="font-league-gothic text-ivory text-3xl lg:text-4xl uppercase block whitespace-nowrap">
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
                <div class="grid lg:grid-cols-3 gap-8 items-start pt-14 md:pt-24">
                    <!-- Large X Scissors Logo Left -->
                    <div class="flex items-center justify-end mt-auto">
                        <img src="{{ asset('frontend_assets/assets/images/xLeft.svg') }}" alt="X Scissors Logo"
                            class="w-full h-full object-cover max-w-52 max-h-66 z-10 -mt-[56px] sm:-mt-[58px]">
                        <!-- Address -->
                        <div class="flex items-start text-ivory max-w-72 h-[150px] w-full -ml-18">
                            <div
                                class="border border-ivory p-4 px-8 flex flex-col items-center justify-center gap-4 w-full h-full">
                                <svg width="20" height="28" viewBox="0 0 20 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M9.8 0C4.382 0 0 4.382 0 9.8C0 15.638 6.188 23.688 8.736 26.754C9.296 27.426 10.318 27.426 10.878 26.754C13.412 23.688 19.6 15.638 19.6 9.8C19.6 4.382 15.218 0 9.8 0ZM9.8 13.3C7.868 13.3 6.3 11.732 6.3 9.8C6.3 7.868 7.868 6.3 9.8 6.3C11.732 6.3 13.3 7.868 13.3 9.8C13.3 11.732 11.732 13.3 9.8 13.3Z"
                                        fill="#F8EDD2" />
                                </svg>
                                <div class="font-league-gothic text-lg md:text-2xl text-center">
                                    <p>{{ $settings->valueOf('address1') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center - NEXO Logo -->
                    <div class="text-end flex items-end justify-end mt-0 md:mt-auto mb-6 md:mb-0 max-h-[160px]">
                        <img src="{{ asset('frontend_assets/assets/images/nexo-standard.svg') }}" alt="X Scissors Logo"
                            class="w-96 h-[165px] mx-auto">
                        <!-- <img src="./assets/images/nexo-higher-standard.svg" alt="X Scissors Logo"
                                                                    class="w-96 h-48 opacity-80 mx-auto my-auto"> -->
                    </div>

                    <!-- Large X Scissors Logo Right -->
                    <div class="flex items-center justify-center">
                        <!-- Address -->
                        <div class="flex items-start text-ivory h-[150px] max-w-72 w-full -mr-18">
                            <div
                                class="border border-ivory px-2 md:p-4 flex flex-col items-start justify-center gap-1 w-full h-full mt-[29px]">
                                <!-- Phone and Website -->
                                <div class="space-y-2 font-league-gothic text-base md:text-xl">
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

                                        <span>{{ $settings->valueOf('web_address') }}</span>
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
                                    <div class="font-league-gothic text-xl ">
                                        <p>{{ $settings->valueOf('hours_part1') }}</p>
                                        <p>{{ $settings->valueOf('hours_part2') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <img src="{{ asset('frontend_assets/assets/images/xRight.svg') }}" alt="X Scissors Logo"
                            class="w-full h-full object-cover max-w-52 max-h-66 z-10">
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
@endpush
