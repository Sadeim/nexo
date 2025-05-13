@extends('frontend.layout')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection
@section('content')
    <!-- Page Header Start -->
	<div class="page-header parallaxie" style="background-image: url('{{ asset($about->image1) }}');">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<!-- Page Header Box Start -->
					<div class="page-header-box">
						<h1 class="text-anime-style-3" data-cursor="-opaque">contact us</h1>
						<nav class="wow fadeInUp">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
								<li class="breadcrumb-item active" aria-current="page">contact us</li>
							</ol>
						</nav>
					</div>
					<!-- Page Header Box End -->
				</div>
			</div>
		</div>
	</div>
	<!-- Page Header End -->

    <!-- Page Contact Us Start -->
    <div class="page-contact-us bg-radius-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Contact Details Box Start -->
                    <div class="contact-details-box">
                        <!-- Contact Us Image Start -->
                        <div class="contact-us-image">
                            <figure class="image-anime">
                                <img src="{{ $about->image1 }}" alt="">
                            </figure>
                        </div>
                        <!-- Contact Us Image End -->

                        <!-- Contact Info List Start -->
                        <div class="contact-info-list">
                            <!-- Conatct Info Item Start -->
                            <div class="contact-info-item wow fadeInUp">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <img src="images/icon-phone-white.svg" alt="">
                                </div>
                                <!-- Icon Box End -->

                                <!-- Contact Info Content Start -->
                                <div class="contact-info-content">
                                    <p>contact:</p>
                                    <h3>{{ $settings->valueOf('phone') }}</h3>
                                </div>
                                <!-- Contact Info Content End -->
                            </div>
                            <!-- Conatct Info Item End -->

                            <!-- Conatct Info Item Start -->
                            <div class="contact-info-item wow fadeInUp" data-wow-delay="0.2s">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <img src="images/icon-mail-white.svg" alt="">
                                </div>
                                <!-- Icon Box End -->

                                <!-- Contact Info Content Start -->
                                <div class="contact-info-content">
                                    <p>email:</p>
                                    <h3>{{ $settings->valueOf('email') }}</h3>
                                </div>
                                <!-- Contact Info Content End -->
                            </div>
                            <!-- Conatct Info Item End -->

                            <!-- Conatct Info Item Start -->
                            <div class="contact-info-item location-info-item wow fadeInUp" data-wow-delay="0.4s">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <img src="images/icon-location-white.svg" alt="">
                                </div>
                                <!-- Icon Box End -->

                                <!-- Contact Info Content Start -->
                                <div class="contact-info-content">
                                    <p>location:</p>
                                    <h3>{{ $settings->valueOf('address') }}</h3>
                                </div>
                                <!-- Contact Info Content End -->
                            </div>
                            <!-- Conatct Info Item End -->
                        </div>
                        <!-- Contact Info List End -->
                    </div>
                    <!-- Contact Details Box End -->
                </div>

                <div class="col-lg-6">
                    <!-- Contact Form Start -->
                    <div class="contact-form">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">contact us</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">get in touch with us</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Have questions or need assistance? Reach out to us today! We're here to provide expert solutions and friendly support.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Contact Form Start -->
                        <div class="member-contect-form contact-form">
                            <!-- Contact Form Start -->
                            <form id="contact_form" method="POST" class="wow fadeInUp" data-wow-delay="0.4s">
                                <div class="row">
                                    <div class="form-group col-md-12 mb-4">
                                        <input type="text" name="name" class="form-control" id="fname" placeholder="First name" required>
                                    </div>
                            
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone no" required>
                                    </div>
                            
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="E-mail address" required>
                                    </div>
                            
                                    <div class="form-group col-md-12 mb-5">
                                        <textarea name="message" class="form-control" id="message" rows="4" placeholder="Message"></textarea>
                                    </div>
                            
                                    <div class="col-md-12">
                                        <button type="submit" class="btn-default">Send Message</button>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Contact Form End -->
                        </div>
                        <!-- Contact Form End -->
                    </div>
                    <!-- Contact Form End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Contact Us End -->

    <!-- Google Map Start -->
    <div class="google-map bg-radius-section">
        <div class="container-flude">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Google Map Start -->
                    <div class="google-map-iframe">
                        <iframe src="{{ $settings->valueOf('map_embed') }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <!-- Google Map End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Google Map End -->  

@endsection 

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#contact_form').on('submit', function(e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.error-message').remove();

                let isValid = true;
                let formData = {};

                // Validation
                $('#contact_form input, #contact_form textarea').each(function() {
                    let field = $(this);
                    let name = field.attr('name');
                    let value = $.trim(field.val());

                    if (value === '') {
                        field.addClass('is-invalid');
                        field.after(
                            '<small class="text-danger error-message">This field is required.</small>'
                            );
                        isValid = false;
                    } else {
                        formData[name] = value;
                    }
                });

                if (!isValid) return;

                $.ajax({
                    url: "{{ route('contact.submit') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
					         toastr.success(response.message);
                        $('#contact_form')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.after(
                                    '<small class="text-danger error-message">' +
                                    messages[0] + '</small>');
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
