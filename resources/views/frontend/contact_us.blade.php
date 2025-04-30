@extends('frontend.layout')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

@endsection
@section('content')

<main>
    <!-- hero-area start -->
    <section class="at-breadcrumb-area at-breadcrumb-space p-relative">
       <div class="container">
          <div class="row justify-content-center">
             <div class="col-xl-7">
                <div class="at-breadcrumb text-center position-relative">
                   <div class="at-breadcrumb-shape">
                      <img src="{{ asset('frontend_assets/assets/img/breadcurmb/breadcrumb-shape.png') }}" alt="">
                   </div>
                   <h1 class="at-breadcrumb-title text-white z-index-2"><span>House of Corned Beef</span>locations</h1>
                </div>
             </div>
          </div>
       </div>
    </section>
    <!-- hero-area end -->

    <!-- contaat-area start -->
    <section class="at-contact-area at-contact at-space-bottom">
       <div class="container">
          <div class="row">
             <div class="col-xl-12">
                <div class="at-contact-map p-relative">
                   <iframe
                      src="{{ $settings->valueOf('map_embed') }}"
                      width="1300" height="600" style="border:0;" allowfullscreen="" loading="lazy"
                      referrerpolicy="no-referrer-when-downgrade">
                   </iframe>
                   <div class="at-contact-brand">
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
          <div class="at-contact-wrapper at-space-top">
             <div class="row">
                <div class="col-xl-5 col-lg-6">
                   <div class="at-contact-content">
                      <div class="at-contact-title-box mb-60">
                         <div class="at-section-title-shape light-bg text-center">
                            <img src="{{ asset('frontend_assets/assets/img/shape/s-shape-1.png') }}" alt="">
                         </div>
                         <h3 class="at-section-title at-reveal-anim mb-30">Around the world, one plate at a time</h3>
                      </div>
                      <div class="at-contact-info">
                         <div class="at-about-opening">
                            <h3 class="at-about-opening-title mb-25 text-white">Opening Hours:</h3>
                            <p>Mon – Thu: <span>10.00 am – 01:00 am</span></p>
                            <p>Fri – Sun: <span>10:00 am – 02:00 am</span></p>
                         </div>
                      </div>
                      <div class="at-contact-info">
                         <h3 class="at-about-opening-title mb-25 text-white">Contact info:</h3>
                         <a href="#" class="mb-30">{{ $settings->valueOf('address') }},<br>{{ $settings->valueOf('phone') }},{{ $settings->valueOf('email') }}</a>
                      </div>
                   </div>
                </div>
                <div class="col-xl-7 col-lg-6">
                   <div class="at-contact-form ps-xl-4 ms-xl-5 mt-90">
                    <form id="contact-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="at-contact-input mb-20">
                                    <input type="text" name="name" placeholder="Mirana" required>
                                    <span class="at-contact-input-icon text-white"><i class="fas fa-user"></i></span>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="at-contact-input mb-20">
                                    <input type="text" name="number" placeholder="Phone number" required>
                                    <span class="at-contact-input-icon text-white"><i class="fa-solid fa-phone"></i></span>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="at-contact-input mb-20">
                                    <input type="email" name="email" placeholder="Business email" required>
                                    <span class="at-contact-input-icon text-white"><i class="fa-regular fa-envelope-open"></i></span>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="at-contact-input">
                                    <select name="subject" required>
                                        <option value="">Subject</option>
                                        <option>01 Pizza</option>
                                        <option>02 Salads</option>
                                        <option>03 Seafood</option>
                                        <option>04 Burgers</option>
                                        <option>05 Beverages</option>
                                        <option>06 Pasta Dishes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="at-contact-comment">
                                    <textarea name="message" cols="30" rows="10" placeholder="Tell us more about event (Optional)"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="at-contact-action mt-15">
                            <button type="submit" class="at-btn-primary">submit now</button>
                        </div>
                    </form>
                    
                   </div>
                </div>
             </div>
          </div>
       </div>
    </section>
    <!-- contaat-area end -->

    <!-- instagram-area start -->
    <section class="at-instagram-area at-instagram fix">
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
                            @foreach ($instagrams as $instagram) 
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function () {
        $('#contact-form').on('submit', function (e) {
            e.preventDefault();

            let formData = {
                _token: "{{ csrf_token() }}",
                name: $('input[name="name"]').val(),
                number: $('input[name="number"]').val(),
                email: $('input[name="email"]').val(),
                subject: $('select[name="subject"]').val(),
                message: $('textarea[name="message"]').val()
            };

            // validation
            if (!formData.name || !formData.number || !formData.email || !formData.subject) {
                toastr.error("Please fill in all required fields.");
                return;
            }

            $.ajax({
                url: "{{ route('contact.submit') }}",
                type: "POST",
                data: formData,
                success: function (res) {
                    toastr.success("Message sent successfully!");
                    $('#contact-form')[0].reset();
                },
                error: function (xhr) {
                    if (xhr.responseJSON?.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error("An error occurred.");
                    }
                }
            });
        });
    });
</script>

@endsection
