{{--
    A barber's photo, or a stand-in when there isn't one.

    Shared by the homepage card and the barber page so the fallback can never
    differ between them — a barber with no photo should look deliberate in
    both places, not broken in one.

    Expects:
      $barber   Employee
      $classes  extra classes for the wrapper (aspect ratio, rounding)
      $initials font size class for the fallback text
--}}
@php
    $classes  = $classes  ?? 'aspect-[4/5]';
    $initials = $initials ?? 'text-6xl';

    $letters = collect(preg_split('/\s+/', trim($barber->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

@if ($barber->photo_path)
    <img src="{{ asset($barber->photo_path) }}"
         alt="{{ $barber->name }}"
         loading="lazy"
         class="w-full h-full object-cover {{ $classes }}">
@else
    {{-- No photo: evergreen block with ivory initials, in the display face
         used for every other heading on the site. --}}
    <div class="w-full h-full bg-evergreen flex items-center justify-center {{ $classes }}"
         role="img" aria-label="{{ $barber->name }}">
        <span class="font-league-gothic text-ivory {{ $initials }} tracking-widest select-none">
            {{ $letters ?: 'N' }}
        </span>
    </div>
@endif
