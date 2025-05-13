@extends('frontend.layout')

@section('content')
    <!-- Page Header Start -->
    <div class="page-header parallaxie" style="background-image: url('{{ asset($about->image1) }}');">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">About us</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">about us</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- About Us Section Start -->
    <div class="about-us bg-radius-section">
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
                                <li>Mon to Fri <span>09:30 - 07:30</span></li>
                                <li>Saturday <span>09:30 - 07:30</span></li>
                                <li>Sunday <span>closed</span></li>
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

    <!-- Our Team Section Start -->
    <div class="our-team bg-radius-section">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-6">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">Team member</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Meet our skilled handyman team</h2>
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
                                <a href="" data-cursor-text="View">
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
                                        @foreach (json_decode($team->social_links, true) as $platform => $link)
                                            <li><a href="{{ $link }}"><i
                                                        class="fa-brands fa-{{ $platform }}-p"></i></a></li>
                                        @endforeach
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

    <!-- Our Testimonial Section Start -->
    <div class="our-testimonial">
        <div class="our-testimonial-box bg-radius-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <!-- Our Testimonial Image Start -->
                        <div class="our-testimonial-image">
                            <figure class="image-anime">
                                <img src="{{ asset('frontend_assets/images/testimonial-image.jpg') }}" alt="">
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
                                                        <img src="{{ asset('frontend_assets/images/testimonial-quotes.svg') }}"
                                                            alt="">
                                                    </div>
                                                    <!-- Testimonial Quotes End -->
                                                </div>
                                                <!-- Testimonial Author Information End -->

                                                <!-- Testimonial Rating Start -->
                                                <div class="testimonial-rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fa-solid fa-star{{ $i <= $testimonial->rating ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                                <!-- Testimonial Rating End -->

                                                <!-- Testimonial Content Start -->
                                                <div class="testimonial-content">
                                                    <p>“{{ $testimonial->message }}”</p>
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

@endsection
