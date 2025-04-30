@extends('frontend.layout')

@section('content')
<section>
    <div class="container Support my-5" data-aos="zoom-in">
        <div class="row">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="row" data-aos="zoom-out-down">
            <h2 class="title-sec">{{ $page->title }}</h2>
            <div class="mt-3 p-3 p-lg-5 bg-light-primary ">
                <p class="mb-4">
                    {{ $page->content }}
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')

@endsection
