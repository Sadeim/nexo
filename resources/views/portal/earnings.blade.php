@extends($portalLayout)
@section('title', 'Earnings')
@section('heading', 'Earnings')

@section('content')
    {{-- Placeholder by design, in both versions. Nothing in the portal
         computes money yet: the commission and tip rules live in a dozen
         places across the admin reports, and a "quick version" here would be
         a thirteenth that disagrees with the others. It arrives when there is
         one shared service behind it. --}}
    <div class="p-empty">
        <div class="p-empty-title">Coming soon</div>
        Your earnings will appear here once they can be shown to the cent.
    </div>
@endsection
