@extends('frontend.layout')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection
@section('content')
<main>
    <!-- slider-area start -->
    <section class="at-slider-area at-slider-space">
        <div class="at-slider-wrapper p-relative">
            <div class="swiper-container at-slider-active">
                <div class="swiper-wrapper">
                    @foreach($sliders as $slider)
                    <div class="swiper-slide">
                        <div class="at-slider-item at-slider-overlay">
                            <div class="at-slider-bg" data-background="{{ asset($slider->image) }}"></div>
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-6 offset-xl-6 col-lg-7 offset-lg-5">
                                        <div class="at-slider-content z-index-2">
                                            <div class="at-slider-title-box mb-20">
                                                <h1 class="at-slider-title">{{ $slider->title }}<br><span>{{ $slider->subtitle }}</span></h1>
                                            </div>
                                            <div class="at-slider-dsc mb-40 d-md-flex justify-content-center">
                                                <p>{{ $slider->description }}</p>
                                            </div>
                                            <div class="at-slider-action text-center">
                                                <a href="{{ $slider->button_link }}" class="at-slider-btn"><span>{{ $slider->button_text }}</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="at-pagination d-none d-sm-block"></div>
        </div>
    </section>
    <!-- slider-area end -->

    <!-- booking-area start -->
    <section class="at-booking-area at-space">
        <div class="container">
            <div class="row">
                <div class="col-xl-4">
                    <h3 class="at-booking-title text-white">Book a table</h3>
                    <p>World's most delicious dishes you'll want to try</p>
                </div>
                <div class="col-xl-8">
                    <form id="booking-form" method="POST">
                        @csrf
                        <div class="at-booking-box d-lg-flex align-items-center justify-content-end">
                            <div class="at-booking-input">
                                <select name="persons" id="persons" required>
                                    <option value="">Select</option>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }} Person</option>
                                    @endfor
                                </select>
                                <span class="at-booking-icon"><i class="fa-solid fa-angle-down"></i></span>
                            </div>
                            <div class="at-booking-input">
                                <input id="date" name="date" type="date" required>
                                <span class="at-booking-icon"><i class="fa-solid fa-angle-down"></i></span>
                            </div>
                            <div class="at-booking-input">
                                <input id="time" name="time" type="time" required>
                                <span class="at-booking-icon"><i class="fa-solid fa-angle-down"></i></span>
                            </div>
                            <div class="at-about-action text-center">
                                <button type="submit" class="at-btn-primary solid-btn">Book a seat</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </section>
    <!-- booking-area end -->

    <!-- about-area start -->
    <section class="at-about-area">
        <div class="container">
            <div class="at-about-wrapper">
                <div class="row align-items-center">
                    <div class="col-xl-3 col-lg-3 col-md-6 order-0 order-lg-0">
                        <div class="at-about-thumb text-center text-lg-start wow at-anim-top" data-wow-duration="1.5s" data-wow-delay="0.1s">
                            <img src="{{ asset($about->image1) }}" alt="">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 order-2 order-lg-1">
                        <div class="at-about-title-box text-center mb-10">
                            <div class="at-section-title-shape text-center">
                                <img src="{{ asset('frontend_assets/assets/img/shape/s-shape-1.png') }}" alt="">
                            </div>
                            <h3 class="at-section-title mb-20 at-title-animation">{{ $about->title }}</h3>
                            <div class="at-about-content mb-30">
                                <div class="row justify-content-center text-center">
                                    <div class="col-xl-12">
                                        <div class="at-about-tab-button">
                                            <nav>
                                                <div class="nav nav-tab" id="nav-tab" role="tablist">
                                                    <button class="nav-link active" id="nav-First-tab" data-bs-toggle="tab" data-bs-target="#nav-First" type="button" role="tab" aria-controls="nav-First" aria-selected="true">
                                                        {{ $about->tab1_title }}
                                                    </button>
                                                    <button class="nav-link" id="nav-Second-tab" data-bs-toggle="tab" data-bs-target="#nav-Second" type="button" role="tab" aria-controls="nav-Second" aria-selected="false" tabindex="-1">
                                                        {{ $about->tab2_title }}
                                                    </button>
                                                    <button class="nav-link" id="nav-Third-tab" data-bs-toggle="tab" data-bs-target="#nav-Third" type="button" role="tab" aria-controls="nav-Third" aria-selected="false" tabindex="-1">
                                                        {{ $about->tab3_title }}
                                                    </button>
                                                </div>
                                            </nav>
                                        </div>
                                        <div class="tab-content mb-20" id="nav-tabContent">
                                            <div class="tab-pane fade active show" id="nav-First" role="tabpanel" aria-labelledby="nav-First-tab" tabindex="0">
                                                <div class="at-about-item">
                                                    <div class="at-about-text">
                                                        <p>{!! $about->tab1_content !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="nav-Second" role="tabpanel" aria-labelledby="nav-Second-tab" tabindex="0">
                                                <div class="at-about-item">
                                                    <div class="at-about-text">
                                                        <p>{!! $about->tab2_content !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="nav-Third" role="tabpanel" aria-labelledby="nav-Third-tab" tabindex="0">
                                                <div class="at-about-item">
                                                    <div class="at-about-text">
                                                        <p>{!! $about->tab3_content !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="at-about-action">
                                <a href="{{ $about->button_link }}" class="at-btn-primary">{{ $about->button_text }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 order-1 order-lg-2 d-none d-md-block">
                        <div class="at-about-thumb-box p-relative text-end">
                            <div class="at-about-img-1">
                                <img src="{{ asset($about->image2) }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about-area end -->

    <!-- categories-area start -->
    <section class="at-categorie-area at-space p-relative">
        <div class="container">
            <div class="at-categorie-wrapper">
                <div class="row">
                    <div class="col-xxl-7 col-xl-6 order-1 order-xl-0">
                        <span class="at-categorie-subtitle mb-75">from our menu</span>
                        <div class="at-categorie-content">
                            @foreach($categories as $category)
                            <div class="at-categorie-item at-hover-reveal-item p-relative">
                                <a href="#">
                                    <span class="at-categorie-text mb-10">{{ $category->name }}</span>
                                    <h3 class="at-categorie-title">{{ $category->description }}</h3>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="at-categorie-thumb wow at-anim-right" data-wow-duration="1.5s" data-wow-delay="0.1s">
                    <img data-speed="0.85" src="{{ asset('frontend_assets/assets/img/home-01/categ/cat-big-thumb.jpg') }}" alt="">
                </div>
            </div>
        </div>
    </section>
    <!-- categories-area end -->

    <!-- testimonial-area start -->
    <section class="at-testimonial-area at-space green-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6">
                    <div class="at-testimonial-title-box mb-75 text-center">
                        <div class="at-section-title-shapes text-center">
                            <img src="{{ asset('frontend_assets/assets/img/shape/s-shape-1.png') }}" alt="">
                        </div>
                        <h3 class="at-section-title at-title-animation mb-30">Users feedback</h3>
                    </div>
                </div>
            </div>
            <div class="at-testimonial-wrapp p-relative">
                <div class="swiper-container fix at-testimonial-active">
                    <div class="swiper-wrapper">
                        @foreach($testimonials as $testimonial)
                        <div class="swiper-slide">
                            <div class="at-testimonial-item text-center">
                                <div class="row justify-content-center">
                                    <div class="col-xl-7 col-lg-8 col-md-9">
                                        <div class="at-testimonial-review mb-30">
                                            @for($i = 0; $i < $testimonial->rating; $i++)
                                            <span><i class="fa-solid fa-star"></i></span>
                                            @endfor
                                        </div>
                                        <div class="at-testimonial-dsc mb-40">
                                            <p>"{{ $testimonial->message }}"</p>
                                        </div>
                                        <div class="at-testimonial-authore d-flex align-items-center justify-content-center">
                                            <div class="at-testimonial-author-thumb mr-15">
                                                <img src="{{ asset($testimonial->image) }}" alt="">
                                            </div>
                                            <div class="at-testimonial-author-content text-start">
                                                <h3 class="at-testimonial-author-title">{{ $testimonial->name }}</h3>
                                                <span>{{ $testimonial->position }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="at-testimonial-control d-block text-center">
                        <div class="at-testimonial-number mt-60"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonial-area end -->

    <!-- meal-area start -->
    <section class="at-menus-inner-area at-space">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-5 col-lg-5 col-md-8 col-sm-9">
                    <div class="at-menus-left">
                        <div class="at-menus-thumb p-relative" data-tilt>
                            <img src="{{ asset('frontend_assets/assets/img/home-01/meal/meal-1-1.jpg') }}" alt="">
                            <div class="at-menus-brand d-none d-sm-block">
                                <div class="at-circle-logo circle-bg">
                                    <img src="{{ asset('frontend_assets/assets/img/logo/circle-brand.png') }}" alt="">
                                    <div class="logo-icon">
                                        <img src="{{ asset('frontend_assets/assets/img/logo/logo-icon.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-7">
                    <div class="at-menus-list-box">
                        <div class="at-menus-tab-button mb-80">
                            <nav>
                                <div class="nav nav-tab" id="nav-tab2" role="tablist">
                                    @foreach($categories as $index => $category)
                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="nav-tab-{{ $category->id }}" data-bs-toggle="tab" data-bs-target="#nav-{{ $category->id }}" type="button" role="tab" aria-controls="nav-{{ $category->id }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                        {{ $category->name }}
                                    </button>
                                    @endforeach
                                </div>
                            </nav>
                        </div>
                        <div class="tab-content mb-20" id="nav-tabContent2">
                            @foreach($categories as $index => $category)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="nav-{{ $category->id }}" role="tabpanel" aria-labelledby="nav-tab-{{ $category->id }}" tabindex="0">
                                @foreach($category->menuItems as $menuItem)
                                <div class="at-menus-list d-flex align-items-center">
                                    <div class="at-menus-list-thumb mr-20">
                                        <a href="#">
                                            <img src="{{ asset($menuItem->image) }}" alt="">
                                        </a>
                                    </div>
                                    <div class="at-menus-list-content">
                                        <div class="at-menus-title-box d-flex align-items-center justify-content-between">
                                            <h3 class="menus-title">
                                                <a href="#">{{ $menuItem->name }}</a>
                                                <span></span>
                                            </h3>
                                            <span class="at-menus-price">${{ number_format($menuItem->price, 2) }}</span>
                                        </div>
                                        <p class="at-menus-dsc">{{ $menuItem->description }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        <a href="#" class="at-btn-secondary at-line-hover">Explore More dish</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- meal-area end -->

    <!-- event-area start -->
    <section class="at-event-area at-event fix at-space-bottom">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-7 col-lg-8 col-md-10 col-sm-11">
                    <div class="at-event-title-box z-index-2 mb-80 text-center">
                        <div class="at-section-title-shape text-center">
                            <img src="{{ asset('frontend_assets/assets/img/shape/s-shape-1.png') }}" alt="">
                        </div>
                        <h3 class="at-section-title at-title-animation mb-30">Explore our luxury events <br>to find more</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="at-event-wrapp">
            <div class="swiper-container at-event-active">
                <div class="swiper-wrapper">
                    @foreach($events as $event)
                    <div class="swiper-slide">
                        <div class="at-event-item">
                            <div class="at-event-thumb mb-40">
                                <a href="#">
                                    <img src="{{ asset($event->image) }}" alt="">
                                </a>
                            </div>
                            <div class="at-event-content text-center">
                                <h3 class="at-event-title mb-25">
                                    <a class="at-line-hover" href="#">{{ $event->title }}</a>
                                </h3>
                                <span class="at-event-meta mb-10">{{ $event->date }}</span>
                                <span class="at-event-meta">Time: <span>{{ $event->time }}</span></span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- event-area end -->

    <!-- contaat-area start -->
    <section class="at-contact-area at-space">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-lg-6 order-1 order-lg-0">
                    <div class="at-contact-map d-flex justify-content-center fix wow at-anim-top" data-wow-duration="1.4s" data-wow-delay="0.1s">
                        <iframe src="{{ $settings->valueOf('map_embed') }}" width="500" height="700" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6 order-0 order-lg-1">
                    <div class="at-contact-wrapp ml-70 text-center text-lg-start">
                        <div class="at-contact-title-box mb-80">
                            <div class="at-section-title-shape light-bg text-center">
                                <img src="{{ asset('frontend_assets/assets/img/shape/s-shape-1.png') }}" alt="">
                            </div>
                            <h3 class="at-section-title at-title-animation mb-30">Find us anytime here <br> in this dope place</h3>
                        </div>
                        <div class="at-contact-thumb mb-60 wow at-anim-top" data-wow-duration="1.5s" data-wow-delay="0.1s">
                            <img src="{{ asset('frontend_assets/assets/img/home-01/contact/contact-thumb.jpg') }}" alt="">
                        </div>
                        <p class="mb-40">
                            <a href="tel:{{ $settings->valueOf('phone') }}">{{ $settings->valueOf('address') }}, {{ $settings->valueOf('phone') }}</a>
                            <br><a href="mailto:{{ $settings->valueOf('email') }}">{{ $settings->valueOf('email') }}</a>
                        </p>
                        <div class="at-contact-bottom d-sm-flex align-items-end justify-content-between">
                            <div class="at-about-opening">
                                <h3 class="at-about-opening-title mb-25 text-white">Opening Hours:</h3>
                                {{ $settings->valueOf('opening_hours') }}
                            </div>
                            <div class="at-contact-action">
                                <a href="#" class="at-btn-primary">get direction</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contaat-area end -->

    <!-- brand-area start -->
    <div class="at-brand-area">
        <div class="container">
            <div class="at-brand-wrapp pt-40 pb-40 mb-25 fix">
                <div class="swiper-container at-brand-active">
                    <div class="swiper-wrapper slide-transtion">
                        @foreach($clients as $client)
                        <div class="swiper-slide">
                            <div class="at-brand-thumb">
                                <a href="#">
                                    <img src="{{ asset($client->logo) }}" alt="">
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <p class="at-section-note mb-0 text-center"><span class="text-white">{{ $client->count() }}+</span> happy sponsors with our amazing team</p>
        </div>
    </div>
    <!-- brand-area end -->

    <!-- instagram-area start -->
    <section class="at-instagram-area fix at-space-top">
        <div class="container-fluid gx-0">
            <div class="row">
                <div class="col-xl-12">
                    <div class="at-instagram-wrapp p-relative">
                        <div class="at-instagram-box">
                            <h3 class="at-instagram-title z-index-2">Instagram</h3>
                            <span class="at-instagram-circle"></span>
                        </div>
                        <div class="at-instagram-slider">
                            <div class="swiper-container at-instagram-active">
                                <div class="swiper-wrapper">
                                    @foreach($instagrams as $instagram)
                                    <div class="swiper-slide">
                                        <div class="at-instagram-item">
                                            <div class="at-instagram-thumb">
                                                <a href="{{ $instagram->link }}">
                                                    <img src="{{ asset($instagram->image) }}" alt="">
                                                </a>
                                            </div>
                                            <div class="at-instagram-social">
                                                <a href="{{ $instagram->link }}">
                                                    <i class="fa-brands fa-instagram"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- instagram-area end -->
</main>
@endsection

@section('js')
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function () {
        $('#booking-form').on('submit', function (e) {
            e.preventDefault();

            let persons = $('#persons').val();
            let date = $('#date').val();
            let time = $('#time').val();

            // validation
            if (!persons || !date || !time) {
                toastr.error("Please fill in all required fields.");
                return;
            }

            $.ajax({
                url: "{{ route('bookings.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    persons: persons,
                    date: date,
                    time: time
                },
                success: function (response) {
                    toastr.success("Booking created successfully!");
                    $('#booking-form')[0].reset(); // clear form
                },
                error: function (xhr) {
                    if (xhr.responseJSON?.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error("An error occurred while booking.");
                    }
                }
            });
        });
    });
</script>

<!-- Add your scripts here -->
<script>
    // Initialize sliders and other JS functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize main slider
        new Swiper('.at-slider-active', {
            loop: true,
            pagination: {
                el: '.at-pagination',
                clickable: true,
            },
        });

        // Initialize testimonial slider
        new Swiper('.at-testimonial-active', {
            loop: true,
            pagination: {
                el: '.at-testimonial-number',
                type: 'fraction',
            },
        });

        // Initialize event slider
        new Swiper('.at-event-active', {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            breakpoints: {
                0: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                }
            }
        });

        // Initialize brand slider
        new Swiper('.at-brand-active', {
            slidesPerView: 5,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                0: {
                    slidesPerView: 2,
                },
                576: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 4,
                },
                992: {
                    slidesPerView: 5,
                }
            }
        });

        // Initialize instagram slider
        new Swiper('.at-instagram-active', {
            slidesPerView: 4,
            spaceBetween: 0,
            loop: true,
            breakpoints: {
                0: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                992: {
                    slidesPerView: 4,
                }
            }
        });
    });
</script>
@endsection
