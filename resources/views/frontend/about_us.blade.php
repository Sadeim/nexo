@extends('frontend.layout')

@section('content')
    <div class="breatcam-section d-flex align-items-center" style="background-image: url('{{ asset($about->image1) }}');">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-lg-12">
                    <div class="breatcam-content">
                        <h1> About Us </h1>
                        <ul>
                            <li><a href="{{ route('home') }}"> <i class="fas fa-home"></i> Home</a></li>
                            <li> About Us </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="about-section upp">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="about-right-thumb">
                        <img src="{{ asset($about->image1) }}" alt="">
                        {{-- <div class="about-counter">
                            <h2 class="counter">795</h2>
                            <h2 class="counter1">+</h2>
                            <span class="counter-text">Project Completed</span>
                        </div> --}}
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                  <div class="hendre-section-title">
                    <h4>ABOUT US</h4>
                    {!! $about->title !!}
                    <p>{{ $about->description }}</p>
                  </div>
                  <div class="about-items">
                    <div class="about-icon">
                      <img src="{{ asset($about->image2) }}" alt="">
                    </div>
                    <div class="about-item-content">
                      <h2 class="about-item-title">{{ $about->tab1_title }}</h2>
                      <p class="about-discription">{{ $about->tab1_content }}</p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6">
                      <div class="about-item-list">
                        <ul>
                          <li><i class="bi bi-check-circle-fill"></i> {{ $about->tab2_title }}</li>
                          <li><i class="bi bi-check-circle-fill"></i> {{ $about->tab2_content }}</li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                      <div class="about-item-list">
                        <ul>
                          <li><i class="bi bi-check-circle-fill"></i> {{ $about->tab3_title }}</li>
                          <li><i class="bi bi-check-circle-fill"></i> {{ $about->tab3_content }}</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="hendre-button">
                    <a href="{{ $about->button_link }}">{{ $about->button_text }} <i class="bi bi-plus"></i></a>
                  </div>
                </div>
            </div>
        </div>
    </div>


    <div class="team-section upper">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12">
                    <div class="hendre-section-title white">
                        <h4> Our Team </h4>
                        <h1> Meet Our Experts </h1>
                        <h1 class="sections"> Team <span>Member</span> </h1>
                        <p>Competently repurpose go forward benefits without goal-oriented ROI the conveniently target
                            business opportunities whereas proactive</p>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12">
                    <div class="row">
                        <div class="team-list owl-carousel">
                            @forelse ($teams as $item)
                                <div class="col-lg-12">
                                    <div class="single-team-box">
                                        <div class="team-thumb">
                                            <img src="{{ asset($item->image) }}" alt="">
                                            <ul class="team-social-list">
                                                @foreach (json_decode($item->social_links, true) as $platform => $link)
                                                    <li><a href="{{ $link }}"><i class="fab fa-{{ $platform }}-f"></i></a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="team-content">
                                            <h3 class="team-title">{{ $item->name }}</h3>
                                            <p class="team-text">{{ $item->position }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                No team members found
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="testimonial-section style-two">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="hendre-section-title  padding-lg">
                        <h4> Testimonial</h4>
                        <h1> What Saying Our Clients </h1>
                        <h1 class="sections"> About <span>Hendre?</span> </h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="testi-list2 owl-carousel">
                    @forelse ($testimonials as $testimonial)
                        <div class="col-lg-12">
                            <div class="teastimonial-single-box2">
                                <div class="people">
                                    <img src="{{ asset($testimonial->photo) }}" alt="">
                                </div>
                                <div class="people-info">
                                    <h4 class="people-name">{{ $testimonial->name }}</h4>
                                    <span class="user-sector">{{ $testimonial->position }}</span>
                                    <div class="testi-icon2">
                                        <i class="bi bi-quote"></i>
                                    </div>
                                </div>
                                <div class="testi-content">
                                    <p class="testi-desc">{{ $testimonial->message }}</p>
                                    @php
                                        $rating = $testimonial->rating ?? 4;
                                    @endphp

                                    <div class="testi-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $rating)
                                                <i class="bi bi-star-fill text-warning"></i> {{-- نجمة ممتلئة --}}
                                            @else
                                                <i class="bi bi-star text-muted"></i> {{-- نجمة فارغة --}}
                                            @endif
                                        @endfor
                                    </div>

                                </div>
                                <div class="testi-shp">
                                    <img src="{{ asset('frontend_assets/assets/images/resource/dot-1.png') }}"
                                        alt="">
                                    <img class="dots"
                                        src="{{ asset('frontend_assets/assets/images/resource/dot-2.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    @empty
                        No Testimonials Found
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
