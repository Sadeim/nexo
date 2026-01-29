@extends('frontend.layouts.app')

@push('styles')
@endpush

@section('content')
    <main class="overflow-x-hidden">
        <section id="gallery-section" class="relative py-16 lg:py-32 overflow-hidden cursor-pointer">
            <div class="absolute inset-0">
                <img src="{{ asset('frontend_assets/assets/images/bg-about.png') }}" alt="Background pattern"
                    class="w-full h-full object-cover">
            </div>

            <div class="conatiner mx-auto relative z-20 h-full overflow-hidden">

                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-0 pointer-events-none w-full max-w-6xl px-4">
                    <img src="{{ asset('frontend_assets/assets/images/Sentences.png') }}" alt="NEXO"
                        class="w-full h-auto object-contain">
                </div>

                <div id="gallery-view-one"
                    class="style-animate-one grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8 lg:gap-16 h-full h-screen transform rotate-30 origin-center relative z-10">

                    @php
                        // Divide works into 4 columns
                        $columns = [[], [], [], []];
                        foreach ($works as $index => $work) {
                            $columns[$index % 4][] = $work;
                        }
                    @endphp

                    <!-- Column 1 - Scroll Up -->
                    <div class="flex flex-col gap-4 md:gap-8 xl:gap-16 animate-scroll-up">
                        @foreach ($columns[0] as $work)
                            <img src="{{ asset($work->image) }}" alt="Gallery Image" class="w-full aspect-3/4 object-cover">
                        @endforeach
                        @if (count($columns[0]) > 0)
                            @foreach ($columns[0] as $work)
                                <img src="{{ asset($work->image) }}" alt="Gallery Image"
                                    class="w-full aspect-3/4 object-cover">
                            @endforeach
                        @endif
                    </div>

                    <!-- Column 2 - Scroll Down -->
                    <div class="flex flex-col gap-4 md:gap-8 xl:gap-16 animate-scroll-down">
                        @foreach ($columns[1] as $work)
                            <img src="{{ asset($work->image) }}" alt="Gallery Image" class="w-full aspect-3/4 object-cover">
                        @endforeach
                        @if (count($columns[1]) > 0)
                            @foreach ($columns[1] as $work)
                                <img src="{{ asset($work->image) }}" alt="Gallery Image2"
                                    class="w-full aspect-3/4 object-cover">
                            @endforeach
                        @endif
                    </div>

                    <!-- Column 3 - Scroll Up (Hidden on mobile) -->
                    <div class="hidden lg:flex flex-col gap-4 md:gap-8 xl:gap-16 animate-scroll-up">
                        @foreach ($columns[2] as $work)
                            <img src="{{ asset($work->image) }}" alt="Gallery Image" class="w-full aspect-3/4 object-cover">
                        @endforeach
                        @if (count($columns[2]) > 0)
                            @foreach ($columns[2] as $work)
                                <img src="{{ asset($work->image) }}" alt="Gallery Image"
                                    class="w-full aspect-3/4 object-cover">
                            @endforeach
                        @endif
                    </div>

                    <!-- Column 4 - Scroll Down (Hidden on mobile) -->
                    <div class="hidden lg:flex flex-col gap-4 md:gap-8 xl:gap-16 animate-scroll-down">
                        @foreach ($columns[3] as $work)
                            <img src="{{ asset($work->image) }}" alt="Gallery Image" class="w-full aspect-3/4 object-cover">
                        @endforeach
                        @if (count($columns[3]) > 0)
                            @foreach ($columns[3] as $work)
                                <img src="{{ asset($work->image) }}" alt="Gallery Image"
                                    class="w-full aspect-3/4 object-cover">
                            @endforeach
                        @endif
                    </div>

                </div>

                <!-- Gallery View Two - Horizontal Scroll -->
                <div id="gallery-view-two"
                    class="style-animate-two flex flex-nowrap justify-center items-center gap-4 md:gap-8 lg:gap-12 w-full max-w-screen px-4 whitespace-nowrap animate-scroll-left hidden relative z-10">
                    @foreach ($works as $work)
                        <img src="{{ asset($work->image) }}" alt="Gallery Image"
                            class="object-cover max-w-106.75 max-h-137.5 shrink-0 h-full">
                    @endforeach
                    @foreach ($works as $work)
                        <img src="{{ asset($work->image) }}" alt="Gallery Image"
                            class="object-cover max-w-106.75 max-h-137.5 shrink-0 h-full">
                    @endforeach
                </div>

                <!-- Gallery View Three - Horizontal Scroll (Smaller) -->
                <div id="gallery-view-three"
                    class="style-animate-two flex flex-nowrap justify-center items-center gap-4 md:gap-8 lg:gap-12 w-full max-w-screen px-4 whitespace-nowrap animate-scroll-left hidden relative z-10">
                    @foreach ($works as $work)
                        <img src="{{ asset($work->image) }}" alt="Gallery Image"
                            class="object-cover max-w-64 max-h-80 shrink-0 h-full">
                    @endforeach
                    @foreach ($works as $work)
                        <img src="{{ asset($work->image) }}" alt="Gallery Image"
                            class="object-cover max-w-64 max-h-80 shrink-0 h-full">
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        const gallerySection = document.getElementById('gallery-section');
        const galleryViewOne = document.getElementById('gallery-view-one');
        const galleryViewTwo = document.getElementById('gallery-view-two');
        const galleryViewThree = document.getElementById('gallery-view-three');

        let currentView = 1; // 1, 2, or 3

        gallerySection.addEventListener('click', function() {
            // Hide all views first
            galleryViewOne.classList.add('hidden');
            galleryViewTwo.classList.add('hidden');
            galleryViewThree.classList.add('hidden');

            // Switch to next view
            if (currentView === 1) {
                // Switch from view 1 to view 2
                galleryViewTwo.classList.remove('hidden');
                currentView = 2;
            } else if (currentView === 2) {
                // Switch from view 2 to view 3
                galleryViewThree.classList.remove('hidden');
                currentView = 3;
            } else {
                // Switch from view 3 back to view 1
                galleryViewOne.classList.remove('hidden');
                currentView = 1;
            }
        });
    </script>
@endpush
