@extends('frontend.layouts.app')

@section('title', $barber->name . ' — Barber at Nexo Barbershop')

@push('meta')
    @php
        // Search results get the opening of the bio, trimmed on a word
        // boundary so it does not stop mid-word.
        $description = $barber->bio
            ? Str::limit(preg_replace('/\s+/', ' ', strip_tags($barber->bio)), 155)
            : $barber->name . ($barber->title ? ', ' . $barber->title : '') . ' at Nexo Barbershop, Arlington Heights.';

        // A barber with no photo falls back to the site's own OG image rather
        // than leaving the tag off, which renders as a bare grey link.
        $ogImage = $barber->photo_path
            ? asset($barber->photo_path)
            : asset($settings->valueOf('browser_icon'));
    @endphp
    <meta name="description" content="{{ $description }}">
    <meta property="og:type" content="profile">
    <meta property="og:title" content="{{ $barber->name }} — Barber at Nexo Barbershop">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ route('barbers.show', $barber->slug) }}">
    <meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')
    <section class="relative bg-evergreen py-16 lg:py-24 overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset($settings->valueOf('green_background')) }}" alt="Background pattern"
                class="w-full h-full object-cover">
        </div>

        <div class="container mx-auto px-4 md:px-8 lg:px-12 xl:px-32 relative z-10">
            <div class="mb-8">
                <a href="{{ route('home') }}#barbers"
                   class="font-inter text-ivory/70 text-sm hover:text-ivory transition-colors">
                    &larr; All barbers
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">
                <!-- Photo -->
                <div class="lg:col-span-5">
                    <div class="overflow-hidden">
                        @include('frontend.partials.barber-photo', [
                            'barber'   => $barber,
                            'classes'  => 'aspect-[4/5]',
                            'initials' => 'text-8xl',
                        ])
                    </div>
                </div>

                <!-- Details -->
                <div class="lg:col-span-7">
                    <h1 class="font-league-gothic text-ivory text-5xl lg:text-6xl tracking-widest">
                        {{ $barber->name }}
                    </h1>

                    @if ($barber->title)
                        <p class="font-inter text-crimson text-lg mt-2 uppercase tracking-wider">{{ $barber->title }}</p>
                    @endif

                    @if ($barber->bio)
                        <div class="font-inter text-ivory/80 leading-relaxed mt-6">
                            {{-- Escaped first, then newlines become breaks: the bio is
                                 free text typed by the owner, not markup. --}}
                            {!! nl2br(e($barber->bio)) !!}
                        </div>
                    @endif

                    <!-- Book -->
                    <div class="mt-8">
                        <button type="button"
                                class="book-now-btn bg-crimson text-ivory font-inter uppercase tracking-wider px-8 py-4 hover:bg-crimson/90 transition-colors"
                                data-barber-id="{{ $barber->id }}"
                                data-barber-name="{{ $barber->name }}">
                            Book with {{ $barber->first_name }}
                        </button>
                    </div>

                    @if ($barber->instagram)
                        <div class="mt-6">
                            <a href="https://instagram.com/{{ $barber->instagram }}"
                               target="_blank" rel="noopener noreferrer"
                               class="font-inter text-ivory/70 hover:text-crimson transition-colors inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.053 1.805.249 2.227.415.56.217.96.477 1.382.9.422.421.682.82.9 1.38.166.423.362 1.058.415 2.228.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.053 1.17-.249 1.805-.415 2.227-.218.56-.478.96-.9 1.382-.422.422-.822.682-1.382.9-.422.166-1.057.362-2.227.415-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.053-1.805-.249-2.227-.415-.56-.218-.96-.478-1.382-.9-.422-.422-.682-.822-.9-1.382-.166-.422-.362-1.057-.415-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.053-1.17.249-1.805.415-2.227.218-.56.478-.96.9-1.382.422-.422.822-.682 1.382-.9.422-.166 1.057-.362 2.227-.415 1.266-.058 1.646-.07 4.85-.07zm0 3.838a6 6 0 100 12 6 6 0 000-12zm0 9.9a3.9 3.9 0 110-7.8 3.9 3.9 0 010 7.8zm7.65-10.137a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/>
                                </svg>
                                &#64;{{ $barber->instagram }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Specialties + hours -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 mt-16">
                @if ($specialties->isNotEmpty())
                    <div class="lg:col-span-7">
                        <h2 class="font-league-gothic text-ivory text-3xl tracking-widest mb-5">SPECIALTIES</h2>
                        <ul class="divide-y divide-ivory/15">
                            @foreach ($specialties as $service)
                                <li class="flex items-baseline justify-between gap-4 py-3">
                                    <span class="font-inter text-ivory">{{ $service->name }}</span>
                                    <span class="font-league-gothic text-ivory text-2xl tracking-wide whitespace-nowrap">
                                        @if ($service->price !== null)
                                            ${{ number_format((float) $service->price, 2) }}
                                        @else
                                            &mdash;
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="{{ $specialties->isNotEmpty() ? 'lg:col-span-5' : 'lg:col-span-7' }}">
                    <h2 class="font-league-gothic text-ivory text-3xl tracking-widest mb-5">SHOP HOURS</h2>
                    <ul class="divide-y divide-ivory/15">
                        @foreach ($shopHours as $entry)
                            <li class="flex items-baseline justify-between gap-4 py-3">
                                <span class="font-inter text-ivory">{{ $entry['days'] }}</span>
                                <span class="font-inter text-ivory/70 whitespace-nowrap">
                                    {{ $entry['open'] }} &ndash; {{ $entry['close'] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
