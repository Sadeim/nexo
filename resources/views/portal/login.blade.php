@extends('portal.layout')
@section('title', 'Sign in')
@section('body_class', 'is-plain')

@section('body')
    <div class="p-centre">
        <div class="p-brand">Nexo</div>
        <div class="p-brand-sub">Barber portal</div>

        @if ($errors->any())
            <div class="p-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('portal.login.post') }}">
            @csrf

            <div class="p-field">
                <label class="p-label" for="phone">Phone</label>
                <input class="p-input @error('phone') p-input-invalid @enderror"
                       id="phone" name="phone" type="tel" inputmode="tel"
                       autocomplete="username" autofocus
                       value="{{ old('phone') }}">
            </div>

            <div class="p-field">
                <label class="p-label" for="password">Password</label>
                <input class="p-input @error('password') p-input-invalid @enderror"
                       id="password" name="password" type="password"
                       autocomplete="current-password">
            </div>

            <button class="p-btn" type="submit">Sign in</button>
        </form>
    </div>
@endsection
