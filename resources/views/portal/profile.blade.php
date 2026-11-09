@extends($portalLayout)
@section('title', 'Profile')
@section('heading', $me->name)
@section('meta', $me->title ?: 'Barber')

@section('content')
    {{-- Exactly what the public page shows, and read-only here. The dashboard
         owns this content; showing it means a barber can see what customers
         see without being able to change it from a phone. --}}
    {{-- Two columns only when there is a photo to put in the first one;
         otherwise the grid reserves an empty 260px gutter. --}}
    <div class="{{ $portalIsDesktop && $me->photo_path ? 'd-profile' : '' }}">

        @if ($me->photo_path)
            <div class="d-profile-media">
                <img class="p-profile-photo" src="{{ asset($me->photo_path) }}" alt="{{ $me->name }}">
            </div>
        @endif

        <div class="d-profile-text">
            @if ($me->bio)
                <div class="p-bio">{{ $me->bio }}</div>
            @endif

            @if (! $me->public_profile)
                <div class="p-note" style="margin-top:14px">
                    Your profile is not shown on the website at the moment.
                </div>
            @endif

            <div class="p-section-title">Account</div>

            <a class="p-btn p-btn-quiet" style="text-decoration:none; text-align:center"
               href="{{ $portalRoute('password.edit') }}">Change password</a>

            {{-- The desktop sidebar already carries a log out, so the button
                 here would be a second one on the same screen. --}}
            @unless ($portalIsDesktop)
                <form method="POST" action="{{ $portalRoute('logout') }}" style="margin-top:10px">
                    @csrf
                    <button class="p-btn p-btn-quiet" type="submit">Log out</button>
                </form>
            @endunless
        </div>
    </div>
@endsection
