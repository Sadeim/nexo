@extends('frontend.layout')

@section('content')

<!-- start: Breadcrumb Section -->
<section class="tj-page-header" data-bg-image="{{ asset('frontend_assets/assets/images/bg/pheader-bg.webp') }}">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="tj-page-header-content text-center">
          <h1 class="tj-page-title">{{ $breadcrumb->page_title ?? 'About Us' }}</h1>
          <div class="tj-page-link">
            <span><i class="tji-home"></i></span>
            <span>
              <a href="{{ route('home') }}">Home</a>
            </span>
            <span>/</span>
            <span>
              <span>{{ $breadcrumb->page_title ?? 'About Us' }}</span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end: Breadcrumb Section -->

<!-- start: About Section -->
<section class="tj-about-section section-gap">
  <div class="container">
    <div class="row align-items-center">
      <!-- صور About -->
      <div class="col-xxl-7 col-lg-6 col-md-12 order-lg-1 order-2">
        <div class="about-img-area wow fadeInUp" data-wow-delay=".3s">
          <div class="about-img">
            <img src="{{ asset($about->image1) }}" alt="">
          </div>
          <div class="about-img">
            <img src="{{ asset($about->image2) }}" alt="">
          </div>
          <div class="about-img">
            <img src="{{ asset($about->image3) }}" alt="">
          </div>
          <div class="circle-text-wrap">
            <span class="circle-text" data-bg-image="{{ asset($about->circle_text_image) }}"></span>
            <span class="logo-icon">
              <img src="{{ asset($about->logo_icon) }}" alt="">
            </span>
          </div>
        </div>
      </div>
      <!-- محتوى About -->
      <div class="col-xxl-5 col-lg-6 col-md-12 order-lg-2 order-1">
        <div class="about-content-area">
          <div class="sec-heading wow fadeInUp" data-wow-delay=".3s">
            <span class="sub-title"><i class="tji-switch-on"></i>ABOUT OUR COMPANY {{ $about->company_name ?? 'Your Company' }}</span>
            <h2 class="sec-title">Reliable in Affordable <span>Electrical</span> Solutions for Clients.</h2>
          </div>
          <div class="about-content wow fadeInUp" data-wow-delay=".5s">
            <p class="desc">{{ $about->description }}</p>
            <div class="about-info">
              <div class="info-left">
                <div class="check-list">
                  <ul>
                    <li><i class="tji-circle-check"></i>{{ $about->check1 }}</li>
                    <li><i class="tji-circle-check"></i>{{ $about->check2 }}</li>
                    <li><i class="tji-circle-check"></i>{{ $about->check3 }}</li>
                  </ul>
                </div>
                <a class="tj-primary-btn" href="route('about.more')" data-text="Learn More">
                  <span class="btn-text">Learn More</span>
                  <span class="btn-icon pulse"><i class="tji-spark"></i></span>
                </a>
              </div>
              <div class="info-right">
                <div class="author-area" data-bg-image="{{ asset('frontend_assets/assets/images/shape/box-pattern.webp') }}">
                  <div class="author-img">
                    <img src="{{ asset('storage/'.$about->author_image) }}" alt="">
                  </div>
                  <div class="author-info">
                    <div class="author-text">
                      <h6 class="author-name">{{ $about->author_name }}</h6>
                      <span>{{ $about->author_position }}</span>
                    </div>
                    <div class="signature">
                      <img src="{{ asset($about->signature_image) }}" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- end about-info -->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end: About Section -->

<!-- start: Achievement Section -->
<section class="tj-achievement-section section-gap" data-bg-image="{{ asset('frontend_assets/assets/images/shape/pattern-bg.webp') }}">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sec-heading text-center wow fadeInUp" data-wow-delay=".3s">
          <span class="sub-title"><i class="tji-switch-on"></i>Award Showcase</span>
          <h2 class="sec-title">Explore <span>ACHIEVEMENTS</span></h2>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="achievement-slider-wrapper wow fadeInUp" data-wow-delay=".4s">
          <div class="swiper swiper-container achievement-slider">
            <div class="swiper-wrapper">
              @foreach ($achievements as $achievement)
                <div class="swiper-slide">
                  <div class="achievement-item">
                    <div class="achievement-inner">
                      <div class="achievement-year">
                        {{ $achievement->year }}<span></span>
                      </div>
                      <div class="achievement-content">
                        <h5 class="title">{{ $achievement->title }}</h5>
                        <div class="desc">
                          <p>{{ $achievement->description }}</p>
                        </div>
                        {{-- <a class="read-more" href="{{ $achievement->read_more_link }}">
                          <span>Read More</span><i class="tji-arrow-right"></i>
                        </a> --}}
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            <div class="swiper-pagination-area"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end: Achievement Section -->

<!-- start: Team Section -->
<section class="tj-team-section team-2">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sec-heading-wrap wow fadeInUp" data-wow-delay=".3s">
          <span class="sub-title"><i class="tji-switch-on"></i>Meet Our Expert</span>
          <div class="heading-wrap-content">
            <div class="sec-heading">
              <h2 class="sec-title">Skilled, Certified, and Dedicated <span>Teams</span></h2>
            </div>
            <div class="btn-wrap">
              <a class="tj-primary-btn" href="route('team.index')" data-text="More member">
                <span class="btn-text">More member</span>
                <span class="btn-icon"><i class="tji-spark"></i></span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      @foreach ($teams as $team)
      <div class="col-lg-3 col-sm-6">
        <div class="team-item wow fadeInUp" data-wow-delay=".4s">
          <div class="team-img">
            <img src="{{ asset($team->image) }}" alt="">
            <div class="social-links style-2">
              <ul>
                @if($team->facebook)
                  <li><a href="{{ $team->facebook }}" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                @endif
                @if($team->instagram)
                  <li><a href="{{ $team->instagram }}" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                @endif
                @if($team->linkedin)
                  <li><a href="{{ $team->linkedin }}" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a></li>
                @endif
                @if($team->twitter)
                  <li><a href="{{ $team->twitter }}" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                @endif
              </ul>
            </div>
          </div>
          <div class="team-content">
            <h5 class="title">{{ $team->name }}</h5>
            <span class="designation">{{ $team->position }}</span>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
<!-- end: Team Section -->

<!-- start: Testimonial Section -->
<section class="tj-testimonial-section section-gap" data-bg-image="{{ asset('frontend_assets/assets/images/shape/pattern-bg.webp') }}">
  <div class="container">
    <div class="row justify-content-between">
      <div class="col-12">
        <div class="sec-heading text-center wow fadeInUp" data-wow-delay=".3s">
          <span class="sub-title"><i class="tji-switch-on"></i>Customer Reviews & <span>Testimonials</span></span>
          <h2 class="sec-title">Customer Reviews & <span>Testimonials</span></h2>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="testimonial-area">
          <div class="testimonial-img wow fadeInUp" data-wow-delay=".3s">
            <img src="{{ asset('frontend_assets/assets/images/testimonial/testimonial-2.webp') }}" alt="">
          </div>
          <div class="testimonial-navigation style-2 d-inline-flex wow fadeInUp" data-wow-delay=".4s">
            <div class="slider-prev">
              <span class="anim-icon">
                <i class="tji-arrow-left"></i>
                <i class="tji-arrow-left"></i>
              </span>
            </div>
            <div class="slider-next">
              <span class="anim-icon">
                <i class="tji-arrow-right"></i>
                <i class="tji-arrow-right"></i>
              </span>
            </div>
          </div>
          <div class="testimonial-wrapper-2 wow fadeIn" data-wow-delay=".4s">
            <div class="swiper swiper-container testimonial-slider-2">
              <div class="swiper-wrapper">
                @foreach ($testimonials as $testimonial)
                  <div class="swiper-slide">
                    <div class="testimonial-item">
                      <div class="testimonial-author">
                        <div class="author-inner">
                          <div class="author-img">
                            <img src="{{ asset($testimonial->photo) }}" alt="">
                          </div>
                          <div class="author-header">
                            <h6 class="title">{{ $testimonial->name }}</h6>
                            <span class="designation">{{ $testimonial->position }}</span>
                          </div>
                        </div>
                        <span class="quote-icon"><i class="tji-quote"></i></span>
                      </div>
                      <div class="desc">
                        <p>“{{ $testimonial->message }}”</p>
                      </div>
                      <div class="rating-area">
                        <div class="star-ratings">
                          <div class="fill-ratings" style="width: {{ $testimonial->rating * 20 }}%">
                            <span>★★★★★</span>
                          </div>
                          <div class="empty-ratings">
                            <span>★★★★★</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="swiper-pagination-area d-none"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end: Testimonial Section -->

<!-- start: Faq Section -->
<section class="tj-faq-section section-gap">
  <div class="container">
    <div class="row justify-content-between">
      <div class="col-xxl-4 col-lg-5">
        <div class="content-wrap wow fadeInUp" data-wow-delay=".3s">
          <div class="sec-heading">
            <span class="sub-title"><i class="tji-switch-on"></i>Read our faq</span>
            <h2 class="sec-title">Got <span>Questions?</span> We Have Got Answer</h2>
          </div>
          <p class="desc">Discover the Difference with Electric <br> Services. Your Trusted Local.</p>
          <a class="tj-primary-btn" href="route('contact')" data-text="Contact us">
            <span class="btn-text">Contact us</span>
            <span class="btn-icon pulse"><i class="tji-spark"></i></span>
          </a>
        </div>
      </div>
      <div class="col-xxl-7 col-lg-7">
        <div class="accordion tj-faq wow fadeInUp" data-wow-delay=".4s" id="faqOne">
          @foreach ($faqs as $index => $faq)
            <div class="accordion-item {{ $index === 0 ? 'active' : '' }}">
              <button class="faq-title {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $index + 1 }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                {{ $faq->question }}
              </button>
              <div id="faq-{{ $index + 1 }}" class="collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqOne">
                <div class="accordion-body faq-text">
                  <p>{{ $faq->answer }}</p>
                </div>
              </div>
              <div class="faq-bg" data-bg-image="{{ asset('frontend_assets/assets/images/shape/faq-item-bg.webp') }}"></div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end: Faq Section -->

<!-- start: Cta Section -->
<section class="tj-cta-section">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="cta-area wow fadeInUp" data-wow-delay=".3s" data-bg-image="{{ asset('frontend_assets/assets/images/shape/cta-bg.webp') }}">
          <div class="sec-heading">
            <h2 class="sec-title"><span>Light Up</span> Your Space <br> Call Us Now!</h2>
            <a class="call-btn" href="tel:123456987"><i class="tji-phone"></i></a>
          </div>
          <div class="cta-img" data-bg-image="{{ asset('frontend_assets/assets/images/shape/cta-img.webp') }}"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end: Cta Section -->

@endsection
