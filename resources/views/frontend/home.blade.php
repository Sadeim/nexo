@extends('frontend.layout')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection
@section('content')
    <!-- Hero Section Start -->
    <div class="hero parallaxie" style="background-image: url({{ asset($slider->image) }}) !important;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <!-- Hero Content Start -->
                    <div class="hero-content">
                        <!-- Section Title Start -->
                        <div class="section-title dark-section">
                            <h3 class="wow fadeInUp">{{ $slider->subtitle }}</h3>
                            <h1 class="text-anime-style-3" data-cursor="-opaque">{{ $slider->title }}</h1>
                        </div>
                        <!-- Section Title End -->

                        <!-- Hero Button Start -->
                        <div class="hero-btn wow fadeInUp" data-wow-delay="0.2s">
                            <a href="{{ $slider->button_link }}" class="btn-default">{{ $slider->button_text }}</a>
                            <a href="{{ route('contact') }}" class="btn-default btn-highlighted">contact now</a>
                        </div>
                        <!-- Hero Button End -->

                        <!-- Hero List Start -->
                        <div class="hero-list wow fadeInUp" data-wow-delay="0.4s">
                            <ul>
                                <li>{{ $slider->description }}</li>
                            </ul>
                        </div>
                        <!-- Hero List End -->
                    </div>
                    <!-- Hero Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Section End -->

    <!-- Client Slider Section Start -->
    <div class="client-slider bg-radius-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <!-- Client Slider Boxes Start -->
                    <div class="client-slider-boxes">
                        <!-- Client Slider Box Start -->
                        <div class="client-slider-box">
                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    @forelse ($clients as $client)
                                        <div class="swiper-slide">
                                            <div class="client-logo">
                                                <img src="{{ asset($client->logo) }}" alt="">
                                            </div>
                                        </div>
                                    @empty
                                        No Data
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- Client Slider Box End -->

                        <!-- Scroll Down Circle Box Start -->
                        <div class="scroll-down-circle-box">
                            <!-- Scroll Circle Text Start -->
                            <div class="scroll-circle-text">
                                <figure>
                                    <img src="{{ url($settings->valueOf('company_logo')) }}" alt="">
                                </figure>

                                <!-- Scroll Down Arrow Start -->
                                {{-- <div class="scroll-down-arrow">
                                    <a href="#about-us">
                                        <i class="fa-solid fa-arrow-down"></i>
                                    </a>
                                </div> --}}
                                <!-- Scroll Down Arrow End -->
                            </div>
                            <!-- Scroll Circle Text End -->
                        </div>
                        <!-- Scroll Down Circle Box End -->

                        <!-- Client Slider Box Start -->
                        <div class="client-slider-box">
                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    @forelse ($clients as $client)
                                        <div class="swiper-slide">
                                            <div class="client-logo">
                                                <img src="{{ asset($client->logo) }}" alt="">
                                            </div>
                                        </div>
                                    @empty
                                        No Data
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- Client Slider Box End -->
                    </div>
                    <!-- Client Slider Boxes End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Client Slider Section End -->

    <!-- About Us Section Start -->
    <div class="about-us" id="about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- About Us Content Start -->
                    <div class="about-us-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">about us</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $about->title }}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $about->description }}</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- About Us Info List Start -->
                        <div class="about-us-info-list">
                            <div class="about-us-info-item wow fadeInUp" data-wow-delay="0.4s">
                                <h3>{{ $about->tab1_title }}</h3>
                                <p>{{ $about->tab1_content }}</p>
                            </div>

                            <div class="about-us-info-item wow fadeInUp" data-wow-delay="0.6s">
                                <h3>{{ $about->tab2_title }}</h3>
                                <p>{{ $about->tab2_content }}</p>
                            </div>
                        </div>
                        <!-- About Us Info List End -->

                        <!-- About Us Button Start -->
                        <div class="about-us-btn wow fadeInUp" data-wow-delay="0.8s">
                            <a href="{{ $about->button_link }}" class="btn-default">{{ $about->button_text }}</a>
                        </div>
                        <!-- About Us Button End -->
                    </div>
                    <!-- About Us Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- About Us Images Start -->
                    <div class="about-us-images">
                        <!-- About Image 1 Start -->
                        <div class="about-img-1">
                            <figure class="image-anime">
                                <img src="{{ asset($about->image1) }}" alt="About Image 1">
                            </figure>
                        </div>
                        <!-- About Image 1 End -->

                        <!-- About Image 2 Start -->
                        <div class="about-img-2">
                            <figure class="image-anime">
                                <img src="{{ asset($about->image2) }}" alt="About Image 2">
                            </figure>
                        </div>
                        <!-- About Image 2 End -->

                        <!-- Company Timing Start -->
                        <div class="company-timing">
                            <h3>Opening hours</h3>
                            <ul>
                                @foreach($about->openingHours as $day => $hours)
                                    @if(!empty($hours['from']) && !empty($hours['to']))
                                        <div class="mb-2" style="color: #fff;">
                                            {{  $hours->day }}
                                            {{ $hours['from'] }} - {{ $hours['to'] }}
                                        </div>
                                    @endif
                                @endforeach
                            </ul>
                            <figure>
                                <img src="{{ asset('frontend_assets/images/icon-clock.svg') }}" alt="">
                            </figure>
                        </div>

                        <!-- Company Timing End -->
                    </div>
                    <!-- About Us Images End -->
                </div>
            </div>
        </div>
    </div>

    <!-- About Us Section End -->
    @if ($sections['services_section']?->is_active)
        <!-- Our Services Section Start -->
        <div class="our-services bg-radius-section">
            <div class="container">
                <div class="row section-row align-items-center">
                    <div class="col-lg-6">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Our Services</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['services_section']->title }}</h2>
                        </div>
                        <!-- Section Title End -->
                    </div>

                    <div class="col-lg-6">
                        <!-- Section Title Content Start -->
                        <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
                            <p>{{ $sections['services_section']->description }}</p>
                        </div>
                        <!-- Section Title Content End -->
                    </div>
                </div>

                <div class="row">
                    @foreach ($services as $index => $service)
                        <div class="col-lg-3 col-md-6">
                            <!-- Service Item Start -->
                            <div class="service-item wow fadeInUp" data-wow-delay="{{ $index * 0.2 }}s">
                                <!-- Service Item Header Start -->
                                <div class="service-item-header">
                                    {{--                                    <div class="icon-box">--}}
                                    {{--                                        <img src="{{ asset($service->icon) }}" alt="{{ $service->name }}">--}}
                                    {{--                                    </div>--}}

                                    <div class="service-item-content">
                                        <h3><a href="#">{{ $service->name }}</a></h3>
                                        <p>{{ \Illuminate\Support\Str::limit($service->description, 80) }}</p>
                                    </div>
                                </div>
                                <!-- Service Item Header End -->

                                <!-- Service Image Start -->
                                <div class="service-image">
                                    {{--                                    <a href="#" data-cursor-text="View">--}}
                                    <figure class="image-anime">
                                        <img src="{{ asset($service->image) }}" alt="{{ $service->name }}">
                                    </figure>
                                    {{--                                    </a>--}}
                                </div>
                                <!-- Service Image End -->
                            </div>
                            <!-- Service Item End -->
                        </div>
                    @endforeach
                </div>

                <div class="col-lg-12">
                    <!-- Service Footer Start -->
                    <div class="service-footer wow fadeInUp" data-wow-delay="0.8s">
                        <p>You will be satisfy with our work. Contact us today <a href="tel:{{$settings->valueOf('phone')}}">{{$settings->valueOf('phone')}}</a></p>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Quick Fact Start -->
    {{--    <div class="quick-facts bg-radius-section">--}}
    {{--        <div class="container">--}}
    {{--            <div class="row section-row align-items-center">--}}
    {{--                <div class="col-lg-6">--}}
    {{--                    <!-- Section Title Start -->--}}
    {{--                    <div class="section-title dark-section">--}}
    {{--                        <h3 class="wow fadeInUp">Some facts</h3>--}}
    {{--                        <h2 class="text-anime-style-3" data-cursor="-opaque">{{$how->name}}</h2>--}}
    {{--                    </div>--}}
    {{--                    <!-- Section Title End -->--}}
    {{--                </div>--}}

    {{--                <div class="col-lg-6">--}}
    {{--                    <!-- Section Title Content Start -->--}}
    {{--                    <div class="section-title-content dark-section wow fadeInUp" data-wow-delay="0.2s">--}}
    {{--                        <p>{{$how->description}}</p>--}}
    {{--                    </div>--}}
    {{--                    <!-- Section Title Content End -->--}}
    {{--                </div>--}}
    {{--            </div>--}}

    {{--            <div class="row align-items-center">--}}
    {{--                <div class="col-lg-3 col-md-6 col-6 order-lg-1 order-md-1 order-1">--}}
    {{--                    <!-- Fact Counter Box Start -->--}}
    {{--                    <div class="fact-counter-box">--}}
    {{--                        <!-- Fact Counter Item Start -->--}}
    {{--                        <div class="fact-counter-item">--}}
    {{--                            <h3>{{$how->tap1_name}}</h3>--}}
    {{--                            <h2><span class="counter">{{$how->tap1_number}}</span>+</h2>--}}
    {{--                            <p>{{$how->tap1_content}}</p>--}}
    {{--                        </div>--}}
    {{--                        <!-- Fact Counter Item End -->--}}

    {{--                        <!-- Fact Counter Item Start -->--}}
    {{--                        <div class="fact-counter-item">--}}
    {{--                            <h3>{{$how->tap2_name}}</h3>--}}
    {{--                            <h2><span class="counter">{{$how->tap2_number}}</span>k</h2>--}}
    {{--                            <p>{{$how->tap2_content}}</p>--}}
    {{--                        </div>--}}
    {{--                        <!-- Fact Counter Item End -->--}}
    {{--                    </div>--}}
    {{--                    <!-- Fact Counter Box End -->--}}
    {{--                </div>--}}


    {{--                <div class="col-lg-3 col-md-6 col-6 order-lg-3 order-md-2 order-2">--}}
    {{--                    <!-- Fact Counter Box Start -->--}}
    {{--                    <div class="fact-counter-box">--}}
    {{--                        <!-- Fact Counter Item Start -->--}}
    {{--                        <div class="fact-counter-item">--}}
    {{--                            <h3>{{$how->tap3_name}}</h3>--}}
    {{--                            <h2><span class="counter">{{$how->tap3_number}}</span>k+</h2>--}}
    {{--                            <p>{{$how->tap3_content}}</p>--}}
    {{--                        </div>--}}
    {{--                        <!-- Fact Counter Item End -->--}}

    {{--                        <!-- Fact Counter Item Start -->--}}
    {{--                        <div class="fact-counter-item">--}}
    {{--                            <h3>{{$how->tap4_name}}</h3>--}}
    {{--                            <h2><span class="counter">{{$how->tap4_number}}</span>%</h2>--}}
    {{--                            <p>{{$how->tap4_content}}</p>--}}
    {{--                        </div>--}}
    {{--                        <!-- Fact Counter Item End -->--}}

    {{--                    </div>--}}

    {{--                    <!-- Fact Counter Box End -->--}}
    {{--                </div>--}}
    {{--                <div class="col-lg-6 order-lg-2 order-md-3 order-3">--}}
    {{--                    <!-- Quick Fact image Start -->--}}
    {{--                    <div class="quick-fact-image">--}}
    {{--                        <img src="{{ asset($how->image) }}" alt="">--}}
    {{--                    </div>--}}
    {{--                    <!-- Quick Fact image End -->--}}
    {{--                </div>--}}

    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    <!-- Quick Fact End -->

    @if ($sections['achievements_section']?->is_active)
        <!-- Best Services Section Start -->
        <div class="best-services bg-radius-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Best Services Box Content Start -->
                        <div class="best-services-box-content">
                            <!-- Best Services Content Start -->
                            <div class="best-services-content">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h3 class="wow fadeInUp">{{ $sections['achievements_section']->title }}</h3>
                                    <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['achievements_section']->description }}</h2>
                                </div>
                                <!-- Section Title End -->

                                <!-- Best Services Body Start -->
                                <div class="best-services-body">
                                    <!-- Contact Now Circle Start -->
                                    <div class="contact-now-circle">
                                        <img src="{{ url($settings->valueOf('company_logo')) }}" alt="">
                                    </div>
                                    <!-- Contact Now Circle End -->

                                    <!-- Best Services Box Start -->
                                    <div class="best-services-box">
                                        @foreach ($achievements as $index => $service)
                                            <!-- Best Services Item Start -->
                                            <div class="best-services-item wow fadeInUp" data-wow-delay="{{ $index * 0.2 }}s">
                                                {{--                                            <div class="icon-box">--}}
                                                {{--                                                <img src="{{ asset($service->icon) }}" alt="{{ $service->name }}">--}}
                                                {{--                                            </div>--}}

                                                <div class="best-services-item-content">
                                                    <h3>{{ $service->name }}</h3>
                                                    <p>{{ $service->description }}</p>
                                                </div>
                                            </div>
                                            <!-- Best Services Item End -->
                                        @endforeach
                                    </div>

                                    <!-- Best Services Box End -->
                                </div>
                                <!-- Best Services Body End -->
                            </div>
                            <!-- Best Services Content End -->

                            <!-- Best Services Image Start -->
                            <div class="best-services-image">
                                <figure class="image-anime">
                                    <img src="{{ asset($sections['achievements_section']->image) }}" alt="">
                                </figure>
                            </div>
                            <!-- Best Services Image End -->
                        </div>
                        <!-- Best Services Box Content End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Best Services Section End -->
    @endif

    @if ($sections['works_section']?->is_active)
        <!-- Our Projects Start -->
        <div class="our-projects bg-radius-section">
            <div class="container">
                <div class="row section-row align-items-center">
                    <div class="col-lg-6">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $sections['works_section']->title }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['works_section']->description }}</h2>
                        </div>
                        <!-- Section Title End -->
                    </div>

                    {{--                    <div class="col-lg-6">--}}
                    {{--                        <!-- Section Title Button Start -->--}}
                    {{--                        <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">--}}
                    {{--                            <a href="#" class="btn-default">explore more work</a>--}}
                    {{--                        </div>--}}
                    {{--                        <!-- Section Title Button End -->--}}
                    {{--                    </div>--}}
                </div>

                <div class="row">
                    @foreach ($works as $index => $work)
                        <div class="col-lg-4 col-md-6">
                            <!-- Project Item Start -->
                            <div class="project-item wow fadeInUp" data-wow-delay="{{ $index * 0.2 }}s">
                                <!-- Project Image Start -->
                                <div class="project-image">
                                    {{--                                    <a href="#" data-cursor-text="View">--}}
                                    <figure class="image-anime">
                                        <img src="{{ asset($work->image) }}" alt="{{ $work->title }}">
                                    </figure>
                                    {{--                                    </a>--}}
                                </div>
                                <!-- Project Image End -->

                                <!-- Project Content Start -->
                                <div class="project-content">
                                    <!-- Project Title Start -->
                                    <div class="project-title">
                                        <h3><a href="#">{{ $work->title }}</a></h3>
                                    </div>
                                    <!-- Project Title End -->

                                    <!-- Project Tag Start -->
                                    <div class="project-tag">
                                        <a href="#" class="btn-default btn-highlighted">{{ $work->category }}</a>
                                    </div>
                                    <!-- Project Tag End -->
                                </div>
                                <!-- Project Content End -->
                            </div>
                            <!-- Project Item End -->
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Our Projects End -->
    @endif

    @if ($sections['testimonials_section']?->is_active)
        <!-- Our Testimonial Section Start -->
        <div class="our-testimonial">
            <div class="our-testimonial-box bg-radius-section">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <!-- Our Testimonial Image Start -->
                            <div class="our-testimonial-image">
                                <figure class="image-anime">
                                    <img src="{{ asset($sections['testimonials_section']->image) }}" alt="">
                                </figure>
                            </div>
                            <!-- Our Testimonial Image End -->
                        </div>

                        <div class="col-lg-6">
                            <!-- Testimonial Slider Start -->
                            <div class="testimonial-slider">
                                <div class="swiper">
                                    <div class="swiper-wrapper" data-cursor-text="Drag">
                                        @foreach ($testimonials as $testimonial)
                                            <div class="swiper-slide">
                                                <!-- Testimonial Item Start -->
                                                <div class="testimonial-item">
                                                    <!-- Testimonial Author Information Start -->
                                                    <div class="testimonial-author-info">
                                                        <!-- Testimonial Author Start -->
                                                        <div class="testimonial-author">
                                                            <div class="author-image">
                                                                <figure class="image-anime">
                                                                    <img src="{{ asset($testimonial->photo) }}" alt="">
                                                                </figure>
                                                            </div>

                                                            <div class="author-content">
                                                                <h3>{{ $testimonial->name }}</h3>
                                                                <p>{{ $testimonial->position }}</p>
                                                            </div>
                                                        </div>
                                                        <!-- Testimonial Author End -->

                                                        <!-- Testimonial Quotes Start -->
                                                        <div class="testimonial-quotes">
                                                            <img src="{{ asset('frontend_assets/images/testimonial-quotes.svg') }}" alt="">
                                                        </div>
                                                        <!-- Testimonial Quotes End -->
                                                    </div>
                                                    <!-- Testimonial Author Information End -->

                                                    <!-- Testimonial Rating Start -->
                                                    <div class="testimonial-rating">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="fa-solid fa-star{{ $i <= $testimonial->rating ? '' : '-o' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <!-- Testimonial Rating End -->

                                                    <!-- Testimonial Content Start -->
                                                    <div class="testimonial-content">
                                                        <p>"{{ $testimonial->message }}"</p>
                                                    </div>
                                                    <!-- Testimonial Content End -->
                                                </div>
                                                <!-- Testimonial Item End -->
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="testimonial-pagination"></div>
                                </div>
                            </div>
                            <!-- Testimonial Slider End -->
                        </div>

                        <div class="col-lg-12">
                            <!-- Agency Support Slider Start -->
                            <div class="testimonial-company-slider">
                                <div class="swiper">
                                    <div class="swiper-wrapper">
                                        @forelse ($clients as $client)
                                            <div class="swiper-slide">
                                                <div class="company-logo">
                                                    <img src="{{ asset($client->logo) }}" alt="">
                                                </div>
                                            </div>
                                        @empty
                                            No Data
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <!-- Agency Support Slider End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Testimonial Section End -->
    @endif

    @if ($sections['teams_section']?->is_active)
        <!-- Our Team Section Start -->
        <div class="our-team bg-radius-section">
            <div class="container">
                <div class="row section-row align-items-center">
                    <div class="col-lg-6">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $sections['teams_section']->title }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['teams_section']->title }}</h2>
                        </div>
                        <!-- Section Title End -->
                    </div>

                    <div class="col-lg-6">
                        <!-- Section Title Button Start -->
                        <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                            <a href="#" class="btn-default">view all member</a>
                        </div>
                        <!-- Section Title Button End -->
                    </div>
                </div>

                <div class="row">
                    @forelse ($teams as $index => $team)
                        <div class="col-lg-4 col-md-6">
                            <!-- Team Member Item Start -->
                            <div class="team-item wow fadeInUp" data-wow-delay="{{ $index * 0.2 }}s">
                                <!-- team Image Start -->
                                <div class="team-image">
                                    <a href="#" data-cursor-text="View">
                                        <figure class="image-anime">
                                            <img src="{{ asset($team->image) }}" alt="">
                                        </figure>
                                    </a>
                                </div>
                                <!-- team Image End -->

                                <!-- Team Body Start -->
                                <div class="team-body">
                                    <!-- Team Content Start -->
                                    <div class="team-content">
                                        <h3><a href="#">{{ $team->name }}</a></h3>
                                        <p>{{ $team->position }}</p>
                                    </div>
                                    <!-- Team Content End -->

                                    <!-- Team Social List Start -->
                                    <div class="team-social-list">
                                        <ul>
                                            @php
                                                $socialLinks = json_decode($team->social_links, true);
                                            @endphp
                                            @if (is_array($socialLinks))
                                                @foreach ($socialLinks as $platform => $link)
                                                    @if (!empty($link))
                                                        <li>
                                                            <a href="{{ $link }}" target="_blank">
                                                                <i class="fa-brands fa-{{ $platform }}"></i>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endif

                                            {{-- @foreach(json_decode($team->social_links, true) as $platform => $link)
                                                <li><a href="{{ $link }}"><i class="fa-brands fa-{{ $platform }}-p"></i></a></li>
                                            @endforeach --}}
                                        </ul>
                                    </div>
                                    <!-- Team Social List End -->
                                </div>
                                <!-- Team Body End -->
                            </div>
                            <!-- Team Member Item End -->
                        </div>
                    @empty
                        No team member found
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Our Team Section End -->
    @endif

    @if ($sections['faqs_section']?->is_active)
        <!-- Our FAQs Section Start -->
        <div class="our-faqs parallaxie" style="background-image: url({{ asset($sections['faqs_section']->image) }})">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-6">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $sections['faqs_section']->title }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['faqs_section']->description }}</h2>
                        </div>
                        <!-- Section Title End -->
                    </div>

                    <div class="col-lg-6">
                        <!-- FAQ Accordion Start -->
                        <div class="faq-accordion" id="accordion">
                            @forelse ($faqs as $index => $faq)
                                <!-- FAQ Item Start -->
                                <div class="accordion-item wow fadeInUp" data-wow-delay="{{ $index * 0.2 }}s">
                                    <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="false" aria-controls="collapse{{ $faq->id }}">
                                            {{ $loop->iteration }}. {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#accordion">
                                        <div class="accordion-body">
                                            <p>{{ $faq->answer }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ Item End -->
                            @empty
                                No faq found
                            @endforelse
                        </div>
                        <!-- FAQ Accordion End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Our FAQs Section End -->
    @endif

    @if ($sections['blog_section']?->is_active)
        <!-- Our Blog Start -->
        <div class="our-blog bg-radius-section">
            <div class="container">
                <div class="row section-row align-items-center">
                    <div class="col-lg-6">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $sections['blog_section']->title }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['blog_section']->description }}</h2>
                        </div>
                        <!-- Section Title End -->
                    </div>

                    <div class="col-lg-6">
                        <!-- Section Title Button Start -->
                        <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                            <a href="{{ route('blog.index') }}" class="btn-default">view all post</a>
                        </div>
                        <!-- Section Title Button End -->
                    </div>
                </div>

                <div class="row">
                    @forelse ($blogs as $blog)
                        <div class="col-lg-4 col-md-6">
                            <!-- Post Item Start -->
                            <div class="post-item wow fadeInUp">
                                <!-- Post Featured Item Start -->
                                <div class="post-featured-image">
                                    <a href="{{ route('blog.show', $blog->slug) }}" data-cursor-text="View">
                                        <figure class="image-anime">
                                            <img src="{{ asset($blog->image) }}" alt="">
                                        </figure>
                                    </a>
                                </div>
                                <!-- Post Featured Image End -->

                                <!-- Team Item Body Start -->
                                <div class="post-item-body">
                                    <!-- Post Meta Start -->
                                    <div class="post-meta">
                                        <ul>
                                            <li>{{ strtoupper(\Carbon\Carbon::parse($blog->created_at)->format('d M, Y')) }}</li>
                                        </ul>
                                    </div>
                                    <!-- Post Meta End -->

                                    <!-- Team Item Content Start -->
                                    <div class="post-item-content">
                                        <h3><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h3>
                                    </div>
                                    <!-- Team Item Content End -->

                                    <!-- Post Item Button Start -->
                                    <div class="post-item-btn">
                                        <a href="{{ route('blog.show', $blog->slug) }}" class="btn-default">read more</a>
                                    </div>
                                    <!-- Post Item Button End -->
                                </div>
                                <!-- Team Item Body End -->
                            </div>
                            <!-- Post Item End -->
                        </div>
                    @empty
                        No blog found
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Our Blog End -->
    @endif
@endsection

@push('js')



@endpush
