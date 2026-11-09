@extends('portal.layouts.auth')
@section('title', 'Sign in')

@section('content')
    @if ($errors->any())
        <div class="p-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ $portalRoute('login.post') }}">
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
@endsection
