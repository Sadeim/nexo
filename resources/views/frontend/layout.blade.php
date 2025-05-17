<!DOCTYPE html>
<html lang="zxx">
<head>
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="Awaiken">
	<!-- Page Title -->
    <title>Hrs</title>
	<!-- Favicon Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ url($settings->valueOf('company_logo')) }}">
	<!-- Google Fonts Css-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<link href="{{ asset('frontend_assets/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('frontend_assets/css/slicknav.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('frontend_assets/css/swiper-bundle.min.css') }}">
	<link href="{{ asset('frontend_assets/css/all.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('frontend_assets/css/animate.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('frontend_assets/css/magnific-popup.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend_assets/css/mousecursor.css') }}">
	<link href="{{ asset('frontend_assets/css/custom.css') }}" rel="stylesheet" media="screen">
	@yield('css')
</head>
<body>

    <!-- Preloader Start -->
	<div class="preloader">
		<div class="loading-container">
			<div class="loading"></div>
			<div id="loading-icon"><img src="{{ url($settings->valueOf('company_logo')) }}" alt=""></div>
		</div>
	</div>
	<!-- Preloader End -->

    <!-- Header Start -->
	<header class="main-header">
		<div class="header-sticky">
			<nav class="navbar navbar-expand-lg" style="align-items: center; margin: 0; padding: 0;">
				<div class="container">
					<!-- Logo Start -->
					<a class="navbar-brand" href="./">
						<img src="{{ asset($settings->valueOf('company_logo')) }}" alt="Logo">
					</a>
					<!-- Logo End -->

					<!-- Main Menu Start -->
					<div class="collapse navbar-collapse main-menu">
                        <div class="nav-menu-wrapper">
                            <ul class="navbar-nav mr-auto" id="menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a>
                                <li class="nav-item"><a class="nav-link" href="{{ route('about_us') }}">About Us</a>
                                <li class="nav-item"><a class="nav-link" href="{{ route('blog.index') }}">Blog</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact Us</a></li>
                            </ul>
                        </div>
                        <!-- Header Btn Start -->
                        <div class="header-btn d-inline-flex">
                            <a href="#" class="btn-default">Get a Quote</a>
                        </div>
                        <!-- Header Btn End -->
					</div>
					<!-- Main Menu End -->
					<div class="navbar-toggle"></div>
				</div>
			</nav>
			<div class="responsive-menu"></div>
		</div>
	</header>
	<!-- Header End -->

	@yield('content')

    <!-- Main Footer Start -->
    <footer class="main-footer bg-radius-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Footer Header Start -->
                    <div class="footer-header">
                        <!-- Footer Logo Start -->
                        <div class="footer-logo">
                            <img src="{{ asset('frontend_assets/images/footer-logo.svg') }}" alt="">
                        </div>
                        <!-- Footer Logo End -->

                        <!-- Footer Contact Box Start -->
                        <div class="footer-contact-box">
                            <!-- Footer Contact Item Start -->
                            <div class="footer-contact-item">
                                <div class="icon-box">
                                    <img src="{{ asset('frontend_assets/images/icon-phone.svg') }}" alt="">
                                </div>

                                <div class="footer-contact-content">
                                    <h3>contact</h3>
                                    <p>{{ $settings->valueOf('phone') }}</p>
                                </div>
                            </div>
                            <!-- Footer Contact Item End -->

                            <!-- Footer Contact Item Start -->
                            <div class="footer-contact-item">
                                <div class="icon-box">
                                    <img src="{{ asset('frontend_assets/images/icon-mail.svg') }}" alt="">
                                </div>

                                <div class="footer-contact-content">
                                    <h3>email</h3>
                                    <p>{{ $settings->valueOf('email') }}</p>
                                </div>
                            </div>
                            <!-- Footer Contact Item End -->
                        </div>
                        <!-- Footer Contact Box End -->
                    </div>
                    <!-- Footer Header End -->
                </div>

                <div class="col-lg-6">
                    <!-- About Footer Start -->
                    <div class="about-footer footer-links">
                        <h3>About Company</h3>
                        <p>{{ $settings->valueOf('site_description') }}</p>
                    </div>
                    <!-- About Footer End -->
                </div>

                <div class="col-lg-2 col-md-4">
                    <!-- Footer Links Start -->
                    <div class="footer-links">
                        <h3>quick link</h3>
                        <ul>
                            <li><a href="{{ route('home') }}">home</a></li>
                            <li><a href="{{ route('about_us') }}">about Us</a></li>
                            <li><a href="#">services</a></li>
                            <li><a href="{{ route('blog.index') }}">blog</a></li>
                        </ul>
                    </div>
                    <!-- Footer Links End -->
                </div>

                <div class="col-lg-2 col-md-4">
                    <!-- Footer Links Start -->
                    <div class="footer-links">
                        <h3>support</h3>
                        <ul>
                            <li><a href="#">help</a></li>
                            <li><a href="#">term's & condition</a></li>
                            <li><a href="#">privacy policy</a></li>
                            <li><a href="{{ route('contact') }}">contact</a></li>
                        </ul>
                    </div>
                    <!-- Footer Links End -->
                </div>

                <div class="col-lg-2 col-md-4">
                    <!-- Footer Links Start -->
                    <div class="footer-links">
                        <h3>follow us</h3>
                        <ul>
                            <li><a href="{{ $settings->valueOf('facebook') }}">facebook</a></li>
                            <li><a href="{{ $settings->valueOf('instagram') }}">instagram</a></li>
                            <li><a href="{{ $settings->valueOf('twitter') }}">twitter</a></li>
                            <li><a href="{{ $settings->valueOf('linkedin') }}">linkedin</a></li>
                        </ul>
                    </div>
                    <!-- Footer Links End -->
                </div>
            </div>

            <!-- Footer Copyright Start -->
            <div class="footer-copyright">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Footer Copyright Text Start -->
                        <div class="footer-copyright-text">
                            <p>Copyright © 2025 All Rights Reserved.</p>
                        </div>
                        <!-- Footer Copyright Text End -->
                    </div>
                </div>
            </div>
            <!-- Footer Copyright End -->
        </div>
    </footer>
    <!-- Main Footer End -->

    <script src="{{ asset('frontend_assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/validator.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/parallaxie.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/gsap.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/magiccursor.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/SplitText.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.mb.YTPlayer.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/function.js') }}"></script>
	@yield('js')
</body>
</html>
