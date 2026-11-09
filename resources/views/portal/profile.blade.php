@extends('portal.layout')
@section('title', 'Profile')

@section('body')
    <header class="p-header">
        <div>
            <h1>{{ $me->name }}</h1>
            <div class="p-sub">{{ $me->title ?: 'Barber' }}</div>
        </div>
    </header>

    <main class="p-main">
        @if (session('portal_status'))
            <div class="p-flash">{{ session('portal_status') }}</div>
        @endif

        {{-- Exactly what the public page shows, and read-only here. The
             dashboard owns this content; showing it means a barber can see
             what customers see without being able to change it from a phone. --}}
        @if ($me->photo_path)
            <img class="p-profile-photo" src="{{ asset($me->photo_path) }}" alt="{{ $me->name }}">
        @endif

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
           href="{{ route('portal.password.edit') }}">Change password</a>

        <form method="POST" action="{{ route('portal.logout') }}" style="margin-top:10px">
            @csrf
            <button class="p-btn p-btn-quiet" type="submit">Log out</button>
        </form>
    </main>

    @include('portal.partials.tabbar')
@endsection
