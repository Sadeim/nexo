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

            <div class="p-section-title">Change password</div>

            {{-- The only place a password is changed. There used to be a
                 screen of its own, reached by a link from here and forced on
                 a barber before they could see anything else; both are gone.
                 One form, in the one place somebody goes looking for it. --}}
            @if ($errors->any())
                <div class="p-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ $portalRoute('password.update') }}">
                @csrf

                <div class="p-field">
                    <label class="p-label" for="current_password">Current password</label>
                    <input class="p-input @error('current_password') p-input-invalid @enderror"
                           id="current_password" name="current_password" type="password"
                           autocomplete="current-password">
                </div>

                <div class="p-field">
                    <label class="p-label" for="password">New password</label>
                    <input class="p-input @error('password') p-input-invalid @enderror"
                           id="password" name="password" type="password"
                           autocomplete="new-password">
                </div>

                <div class="p-field">
                    <label class="p-label" for="password_confirmation">Repeat new password</label>
                    <input class="p-input" id="password_confirmation" name="password_confirmation"
                           type="password" autocomplete="new-password">
                </div>

                <button class="p-btn" type="submit">Save password</button>
            </form>

            {{-- The desktop sidebar already carries a log out, so the button
                 here would be a second one on the same screen. --}}
            @unless ($portalIsDesktop)
                <div class="p-section-title">Account</div>

                <form method="POST" action="{{ $portalRoute('logout') }}">
                    @csrf
                    <button class="p-btn p-btn-quiet" type="submit">Log out</button>
                </form>
            @endunless
        </div>
    </div>
@endsection
