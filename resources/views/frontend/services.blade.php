@extends('frontend.layouts.app')

@push('styles')
@endpush

@section('content')
    <main>
        <!-- Hero Start -->
        <section class="relative py-16 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0">
                <img src="{{ asset('frontend_assets/assets/images/bg-about.png') }}" alt="Background pattern"
                    class="w-full h-full object-cover">
            </div>

            <div class="container mx-auto px-4 md:px-8 lg:px-16 relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-6 w-full">
                        <h2 class="font-league-gothic text-ivory text-3xl lg:text-4xl uppercase">
                            Services
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

                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 my-10">
                    @foreach ($services as $service)
                        <div
                            class="border border-ivory p-6 flex items-center justify-center gap-4 hover:bg-ivory text-ivory hover:text-evergreen transition-colors">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M23.4377 12.5002C23.4402 12.8207 23.3428 13.1339 23.159 13.3964C22.9753 13.659 22.7143 13.8577 22.4123 13.9651L16.217 16.218L13.9651 22.4123C13.8541 22.7114 13.6542 22.9692 13.3924 23.1514C13.1305 23.3335 12.8192 23.4311 12.5002 23.4311C12.1813 23.4311 11.8699 23.3335 11.6081 23.1514C11.3462 22.9692 11.1464 22.7114 11.0354 22.4123L8.78342 16.217L2.58811 13.9651C2.28908 13.8541 2.03119 13.6542 1.84907 13.3924C1.66695 13.1305 1.56934 12.8192 1.56934 12.5002C1.56934 12.1813 1.66695 11.8699 1.84907 11.6081C2.03119 11.3462 2.28908 11.1464 2.58811 11.0354L8.78342 8.78342L11.0354 2.58811C11.1464 2.28908 11.3462 2.03119 11.6081 1.84907C11.8699 1.66695 12.1813 1.56934 12.5002 1.56934C12.8192 1.56934 13.1305 1.66695 13.3924 1.84907C13.6542 2.03119 13.8541 2.28908 13.9651 2.58811L16.218 8.78342L22.4123 11.0354C22.7143 11.1427 22.9753 11.3415 23.159 11.604C23.3428 11.8665 23.4402 12.1798 23.4377 12.5002Z"
                                    fill="currentColor" />
                            </svg>
                            <h3 class="font-league-gothic text-2xl md:text-3xl font-normal tracking-wider uppercase">
                                {{ $service->name }}
                            </h3>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="container mx-auto px-4 md:px-8 lg:px-16 bg-white py-16">
            <div class="flex items-center gap-6">
                <svg width="144" height="221" viewBox="0 0 144 221" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M59.7899 13.4375H76.4197C86.9227 13.4375 95.4059 21.9703 95.4059 32.3844V32.5188H95.6752C104.764 32.5188 112.103 39.8422 112.103 48.9125C112.103 57.9828 104.764 65.3063 95.6752 65.3063H73.4573L41.1403 80.8938V65.3063H40.7364C31.6472 65.3063 24.3086 57.9828 24.3086 48.9125C24.3086 39.8422 31.6472 32.5188 40.7364 32.5188H40.8037V32.3844C40.8037 21.9031 49.2869 13.4375 59.7899 13.4375ZM95.7426 92.1813L41.1403 118.586V133.098L95.7426 106.761V92.1813ZM95.6752 168.909H74.9856L95.7426 158.898V144.386L44.9039 168.909H40.7364C31.6472 168.909 24.3086 176.233 24.3086 185.303C24.3086 194.373 31.6472 201.697 40.7364 201.697H95.6752C104.764 201.697 112.103 194.373 112.103 185.303C112.103 176.233 104.764 168.909 95.6752 168.909Z"
                        fill="#F8EDD2" />
                    <path
                        d="M41.1401 92.4498L95.7423 66.1123V80.6248L41.1401 106.962V92.4498ZM41.1401 144.654L95.7423 118.317V132.829L41.1401 159.167V144.654Z"
                        fill="#F8EDD2" />
                    <path
                        d="M67.7899 13.4375H84.4197C94.9227 13.4375 103.406 21.9703 103.406 32.3844V32.5188H103.675C112.764 32.5188 120.103 39.8422 120.103 48.9125C120.103 57.9828 112.764 65.3063 103.675 65.3063H81.4573L49.1403 80.8938V65.3063H48.7364C39.6472 65.3063 32.3086 57.9828 32.3086 48.9125C32.3086 39.8422 39.6472 32.5188 48.7364 32.5188H48.8037V32.3844C48.8037 21.9031 57.2869 13.4375 67.7899 13.4375ZM103.743 92.1813L49.1403 118.586V133.098L103.743 106.761V92.1813ZM103.675 168.909H82.9856L103.743 158.898V144.386L52.9039 168.909H48.7364C39.6472 168.909 32.3086 176.233 32.3086 185.303C32.3086 194.373 39.6472 201.697 48.7364 201.697H103.675C112.764 201.697 120.103 194.373 120.103 185.303C120.103 176.233 112.764 168.909 103.675 168.909Z"
                        fill="#8C1C13" />
                    <path
                        d="M49.1401 92.4498L103.742 66.1123V80.6248L49.1401 106.962V92.4498ZM49.1401 144.654L103.742 118.317V132.829L49.1401 159.167V144.654Z"
                        fill="#8C1C13" />
                    <path
                        d="M76.7899 19.4375H93.4197C103.923 19.4375 112.406 27.9703 112.406 38.3844V38.5188H112.675C121.764 38.5188 129.103 45.8422 129.103 54.9125C129.103 63.9828 121.764 71.3063 112.675 71.3063H90.4573L58.1403 86.8938V71.3063H57.7364C48.6472 71.3063 41.3086 63.9828 41.3086 54.9125C41.3086 45.8422 48.6472 38.5188 57.7364 38.5188H57.8037V38.3844C57.8037 27.9031 66.2869 19.4375 76.7899 19.4375ZM112.743 98.1813L58.1403 124.586V139.098L112.743 112.761V98.1813ZM112.675 174.909H91.9856L112.743 164.898V150.386L61.9039 174.909H57.7364C48.6472 174.909 41.3086 182.233 41.3086 191.303C41.3086 200.373 48.6472 207.697 57.7364 207.697H112.675C121.764 207.697 129.103 200.373 129.103 191.303C129.103 182.233 121.764 174.909 112.675 174.909Z"
                        fill="#283327" />
                    <path
                        d="M58.1401 98.4498L112.742 72.1123V86.6248L58.1401 112.962V98.4498ZM58.1401 150.654L112.742 124.317V138.829L58.1401 165.167V150.654Z"
                        fill="#283327" />
                </svg>
                <div class="font-league-gothic text-black">
                    <h2 class=" text-3xl lg:text-4xl uppercase">
                        Our Most Popular Services
                    </h2>
                    <p class="text-2xl uppercase text-[#283327]"> {{ $sections['services_section']->note ?? 'Service prices' }}</p>
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
    </main>
@endsection

@push('scripts')
@endpush
