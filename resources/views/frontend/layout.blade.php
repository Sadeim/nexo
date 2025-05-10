<!DOCTYPE HTML>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>HRS</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url($settings->valueOf('company_logo')) }}">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/bootstrap.min.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/owl.carousel.min.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/animate.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/animated-text.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/all.min.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/flaticon.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/theme-default.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/meanmenu.min.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/owl.transitions.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/venobox/venobox.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/bootstrap-icons.css') }}" type="text/css" media="all">
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/style.css') }}" type="text/css" media="all">  
	<link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/responsive.css') }}" type="text/css" media="all">
	<script src="{{ asset('frontend_assets/assets/js/vendor/modernizr-3.5.0.min.js') }}"></script>
   @yield('css')
</head>

<body>
	<!-- loder -->
	<div class="loader-wrapper">
		<div class="loader"></div>
		<div class="loder-section left-section"></div>
		<div class="loder-section right-section"></div>
	</div>

	<!--==================================================-->
	<!-- Start Hendre Top Menu section -->
	<!--==================================================-->
	<div class="header-top-section">
		<div class="container">
			<div class="row align-items-center d-flex">
				<div class="col-lg-6">
					<div class="header-address-info">
						<p> <i class="bi bi-geo-alt"></i> {{ $settings->valueOf('address') }} <span> <i class="bi bi-envelope-open"></i> {{ $settings->valueOf('email') }} </span></p>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="header-top-right text-right">
						<div class="hendre-social-icon">
						<ul>
							<li><a href="{{ $settings->valueOf('facebook') }}"><i class="fab fa-facebook-f"></i></a></li>
							<li><a href="{{ $settings->valueOf('twitter') }}"><i class="fab fa-twitter"></i></a></li>
							<li><a href="{{ $settings->valueOf('linkedin') }}"><i class="fab fa-linkedin-in"></i></a></li>
							<li><a href="{{ $settings->valueOf('pinterest') }}"><i class="fab fa-pinterest-p"></i></a></li>	
						</ul>
					</div>
					<div class="phone-number">
						<p> <i class="fas fa-phone-square-alt"></i> <span>Call Us :</span>{{ $settings->valueOf('phone') }}</p>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--==================================================-->
	<!-- Start Hendre Top Menu section -->
	<!--==================================================-->

	<!--==================================================-->
	<!-- Start Hendre Main Menu  -->
	<!--==================================================-->
	<div id="sticky-header" class="hendre_nav_manu">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-2">
					<div class="logo">
						<a class="logo_img" href="{{ route('home') }}" style="width: 73px" title="hendre">
							<img src="{{ url($settings->valueOf('company_logo')) }}" style="width:65px;" alt="logo">
						</a>
						<a class="main_sticky" href="{{ route('home') }}" title="hendre">
							<img src="{{ url($settings->valueOf('company_logo')) }}" style="width:65px;" alt="logo">
						</a>
					</div>
				</div>
				<div class="col-lg-10">
					<nav class="hendre_menu">
						<ul class="nav_scroll">
							<li><a href="#">Home <span><i class="fas fa-chevron-down"></i></span></a>
								<ul class="sub-menu">
									<li><a href="{{ route('home') }}">HRS</a></li>
								</ul>
							</li>

							<li><a href="{{ route('about_us') }}">About</a></li>
							{{-- <li><a href="#">Services <span><i class="fas fa-chevron-down"></i></span></a>
								<ul class="sub-menu">
									<li><a href="service.html">Our Service</a></li>
									<li><a href="service-details.html">Service Details</a></li>
								</ul>
							</li> --}}
							{{-- <li><a href="#">Pages <span><i class="fas fa-chevron-down"></i></span></a>
								<ul class="sub-menu">
									<li><a href="about.html">About Us</a></li>
									<li><a href="service.html">Our Service</a></li>
									<li><a href="team.html">Our Team</a></li>
									<li><a href="portfolio.html">Project</a></li>
									<li><a href="portfolio-details.html">Project Details</a></li>
									<li><a href="pricing.html">Pricing</a></li>
									<li><a href="faq.html">Faq</a></li>
									<li><a href="contact.html">Contact Us</a></li>
								</ul>
							</li> --}}
							{{-- <li><a href="#">Shop <span><i class="fas fa-chevron-down"></i></span></a>
								<ul class="sub-menu">
									<li><a href="shop.html">Shop One</a></li>
									<li><a href="shop-2.html">Shop Two</a></li>
									<li><a href="shop-details.html">Shop Details</a></li>
								</ul>
							</li> --}}
							{{-- <li><a href="#">Blog <span><i class="fas fa-chevron-down"></i></span></a>
								<ul class="sub-menu">
									<li><a href="blog-grid.html">Blog Gird</a></li>
									<li><a href="blog.html">Blog List</a></li>
									<li><a href="blog-2column.html">Blog-2Column</a></li>
									<li><a href="blog-details.html">Blog Details</a></li>
								</ul>
							</li> --}}
							<li><a href="{{ route('contact') }}">Contact</a></li>
							<li><a href="{{ route('blog.index') }}">Blog</a></li>
						</ul>
						<div class="header-menu-right-btn">
							<!--header-search-->
							<div class="header-search-button search-box-outer">
								<a href="#"><i class="fas fa-search"></i></a>
							</div>
							<!-- header button -->
							<div class="header-button">
								<a href="#">Get a Free Quote</a>
							</div>
						</div>
					</nav>
				</div>
			</div>
		</div>
	</div>

	<!-- hendre Mobile Menu  -->
	<div class="mobile-menu-area sticky d-sm-block d-md-block d-lg-none ">
		<div class="mobile-menu">
			<nav class="hendre_menu">
				<ul class="nav_scroll">
					<li><a href="{{ route('home') }}">Home <span><i class="fas fa-chevron-down"></i></span></a>
						{{-- <ul class="sub-menu">
							<li><a href="{{ route('home') }}">Hendre Home Page</a></li>
							<li><a href="home-landing.html">Hendre Landing Page</a></li>
						</ul> --}}
					</li>
					<li><a href="{{ route('about_us') }}">About Us</a></li>
					{{-- <li><a href="#">Services <span><i class="fas fa-chevron-down"></i></span></a>
						<ul class="sub-menu">
							<li><a href="service.html">Our Service</a></li>
							<li><a href="service-details.html">Service Details</a></li>
						</ul>
					</li> --}}
					{{-- <li><a href="#">Pages <span><i class="fas fa-chevron-down"></i></span></a>
						<ul class="sub-menu">
							<li><a href="about.html">About Us</a></li>
							<li><a href="service.html">Our Service</a></li>
							<li><a href="team.html">Our Team</a></li>
							<li><a href="portfolio.html">Project</a></li>
							<li><a href="portfolio-details.html">Project Details</a></li>
							<li><a href="pricing.html">Pricing</a></li>
							<li><a href="faq.html">Faq</a></li>
							<li><a href="contact.html">Contact Us</a></li>
						</ul>
					</li> --}}
					{{-- <li><a href="#">Shop <span><i class="fas fa-chevron-down"></i></span></a>
						<ul class="sub-menu">
							<li><a href="shop.html">Shop One</a></li>
							<li><a href="shop-2.html">Shop Two</a></li>
							<li><a href="shop-details.html">Shop Details</a></li>
						</ul>
					</li> --}}
					{{-- <li><a href="#">Blog <span><i class="fas fa-chevron-down"></i></span></a>
						<ul class="sub-menu">
							<li><a href="blog-grid.html">Blog Gird</a></li>
							<li><a href="blog.html">Blog List</a></li>
							<li><a href="blog-2column.html">Blog-2Column</a></li>
							<li><a href="blog-details.html">Blog Details</a></li>
						</ul>
					</li> --}}
					<li><a href="#">Contact Us</a></li>
				</ul>
			</nav>
		</div>
	</div>
	<!--==================================================-->
	<!-- End Hendre Main Menu  -->
	<!--==================================================-->


   @yield('content')

	<!--==================================================-->
	<!-- Start Hendre Footer Section  -->
	<!--==================================================-->

	<div class="footer-section"> 
		<div class="container">
			<div class="row subscribe-section">
				<div class="col-lg-5 col-md-12">
					<div class="subscribe-contact-info">
						<div class="subscribe-icon">
							<img src="{{ asset('frontend_assets/assets/images/resource/call.png') }}" alt="">
						</div>
						<div class="subscribe-contact">
							<span class="subscribe-text">For Enquery :</span>
							<h2 class="subscribe-phone-number">{{ $settings->valueOf('phone') }}</h2>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-12">
					<div class="widget-title">
						<h2 class="widget-title"> Subscribe Now </h2>
					</div>
				</div>
				<div class="col-lg-4 col-md-12">
					<div class="subscribe-widget">
						<form id="subscribe-form" method="POST">
							@csrf
							<input type="text" class="src-input-box" placeholder="Enter your email" name="email" id="email">
							<button class="subscribe-btn" type="submit">
								<span>Subscribe</span>
							</button>
						</form>
					</div>
				</div>
			</div>
			<div class="row footer-bg">
				<div class="col-lg-3 col-md-6">
					<div class="widget widgets-company-info">
						<div class="dreamhub-logo">
						<a class="logo_thumb" href="{{ route('home') }}" title="dreamhub">
							<img src="{{ url($settings->valueOf('company_logo')) }}" alt="" />
						</a>
					</div>
						<div class="company-info-desc">
							<p> {{ $settings->valueOf('site_description') }}</p>
						</div>
						<div class="follow-company-icon">
							<a href="{{ $settings->valueOf('facebook') }}"> <i class="fab fa-facebook-f"></i> </a>
							<a href="{{ $settings->valueOf('twitter') }}"> <i class="fab fa-twitter"> </i> </a>
							<a href="{{ $settings->valueOf('linkedin') }}"> <i class="fab fa-linkedin-in"></i> </a>
							<a href="{{ $settings->valueOf('pinterest') }}"> <i class="fab fa-pinterest-p"></i> </a>
						</div>
					</div>					
				</div>
				<div class="col-lg-3 col-md-6 pl-40">
					<div class="widget widget-nav-menu">
						<h4 class="widget-title">Popular Services</h4>
						<div class="menu-quick-link-content">
							<ul class="footer-menu">
								@forelse ($shared_services as $service)
									<li><a href="#"> <i class="bi bi-check-lg"></i> {{ $service->name }} </a></li>
								@empty
									<li><a href="#"> No Services </a></li>
								@endforelse
							</ul>
						</div>
					</div>
				</div>	
				<div class="col-lg-3 col-md-6">
					<div class="widget widget-nav-menu">
						<h4 class="widget-title"> Useful Links </h4>
						<div class="menu-quick-link-content">
							<ul class="footer-menu">
								<li><a href="{{ route('about_us') }}"> About Us </a></li>
								<li><a href="{{ route('contact') }}"> Contact Us </a></li>
								<li><a href="#"> FAQ’s </a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 pr-0">
					<div class="menu-quick-link-contact">
						<!-- widget title -->
						<h4 class="widget-title"> Working Hours </h4>
						<!-- company contact info -->
						<div class="company-work-hour">
							<ul>
								<li>Mon - Wed <span class="table-text">8.00 AM - 5.00 PM</span></li>
								<li>Thu - Fri <span>9.00 AM - 4.00 PM</span></li>
								<li>Saturday <span>9.00 AM - 2.00 PM</span></li>
								<li class="table-brb">Sunday <span>Clossed</span></li>
							</ul>
						</div>
					</div>
				</div>
				{{-- <div class="footer-shape">
					<img src="{{ asset('frontend_assets/assets/images/resource/footer-shp.png') }}" alt="">
				</div>
				<div class="footer-shape2">
					<img src="{{ asset('frontend_assets/assets/images/resource/footer-shp2.png') }}" alt="">
				</div> --}}
			</div>
		</div>
	</div>	

	<div class="footer-bottom-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-6">
					<div class="footer-bottom-content">
						<div class="footer-bottom-content-copy">
							<p>Copyright © 2025 <span>HRS</span>. All rights reserved.</p>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-md-6">
					<div class="footer-bottom-menu text-right">
						<ul>
							<li><a href="#">Terms Condition</a></li>
							<li><a href="#">Privacy Policy</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--==================================================-->
	<!-- End Hendre Footer Section  -->
	<!--==================================================-->



	<!--==================================================-->
	<!-- Start Search Popup Section -->
	<!--==================================================-->
	<div class="search-popup">
		<button class="close-search style-two"><span class="flaticon-multiply"><i class="far fa-times-circle"></i></span></button>
		<button class="close-search"><i class="bi bi-arrow-up"></i></button>
		<form method="post" action="#">
			<div class="form-group">
				<input type="search" name="search-field" value="" placeholder="Search Here" required="">
				<button type="submit"><i class="fa fa-search"></i></button>
			</div>
		</form>
	</div>
	<!--==================================================-->
	<!-- Start Search Popup Section -->
	<!--==================================================-->




	<!--==================================================-->
	<!-- Start scrollup section Section -->
	<!--==================================================-->
	<!-- scrollup section -->
	<div class="scroll-area">
		<div class="top-wrap">
			<div class="go-top-btn-wraper">
				<div class="go-top go-top-button">
					<i class="bi bi-chevron-double-up"></i>
					<i class="bi bi-chevron-double-up"></i>
				</div>
			</div>
		</div>
	</div>
	<!--==================================================-->
	<!-- Start scrollup section Section -->
	<!--==================================================-->





	<script src="{{ asset('frontend_assets/assets/js/vendor/jquery-3.6.2.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/popper.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/jquery.counterup.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/waypoints.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/wow.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/imagesloaded.pkgd.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/venobox/venobox.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/animated-text.js') }}"></script>
	<script src="{{ asset('frontend_assets/venobox/venobox.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/isotope.pkgd.min.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/jquery.meanmenu.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/jquery.scrollUp.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/jquery.barfiller.js') }}"></script>
	<script src="{{ asset('frontend_assets/assets/js/theme.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

   @yield('js')
   <script>
    $(document).ready(function () {
        $('#subscribe-form').on('submit', function (e) {
            e.preventDefault();

            let email = $('#email').val().trim();
            if (!email) {
                toastr.error('Email is required.');
                return;
            }

            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                toastr.error('Please enter a valid email address.');
                return;
            }

            $.ajax({
                url: "{{ route('newsletter.subscribe') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    email: email
                },
                success: function (response) {
                    toastr.success(response.message);
                    $('#subscribe-form')[0].reset();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.email) {
                            toastr.error(errors.email[0]);
                        }
                    } else {
                        toastr.error('Something went wrong.');
                    }
                }
            });
        });
    });
</script>
</body>

</html>