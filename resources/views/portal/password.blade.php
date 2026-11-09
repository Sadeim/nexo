@extends('portal.layout')
@section('title', 'Change password')
@section('body_class', 'is-plain')

@section('body')
    <div class="p-centre">
        <div class="p-brand">Nexo</div>
        <div class="p-brand-sub">{{ $forced ? 'Choose your password' : 'Change password' }}</div>

        @if ($forced)
            <div class="p-note">
                <strong>Set a password only you know.</strong><br>
                The one you were given was typed by the shop, so it is not private yet.
            </div>
        @endif

        @if ($errors->any())
            <div class="p-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('portal.password.update') }}">
            @csrf

            <div class="p-field">
                <label class="p-label" for="current_password">Current password</label>
                <input class="p-input @error('current_password') p-input-invalid @enderror"
                       id="current_password" name="current_password" type="password"
                       autocomplete="current-password" autofocus>
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

        @unless ($forced)
            <form method="POST" action="{{ route('portal.logout') }}" style="margin-top:12px">
                @csrf
                <button class="p-btn p-btn-quiet" type="submit">Back out and log out</button>
            </form>
        @endunless
    </div>
@endsection
