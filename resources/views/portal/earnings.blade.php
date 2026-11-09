@extends('portal.layout')
@section('title', 'Earnings')

@section('body')
    <header class="p-header">
        <div>
            <h1>Earnings</h1>
        </div>
    </header>

    <main class="p-main">
        {{-- Placeholder by design. Nothing in the portal computes money yet:
             the commission and tip rules live in a dozen places across the
             admin reports, and a "quick version" here would be a thirteenth
             that disagrees with the others. It arrives when there is one
             shared service behind it. --}}
        <div class="p-empty">
            <div class="p-empty-title">Coming soon</div>
            Your earnings will appear here once they can be shown to the cent.
        </div>
    </main>

    @include('portal.partials.tabbar')
@endsection
