<!doctype html>
<html class="no-js" lang="zxx" dir="ltr">

<head>

   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>House of Corned Beef</title>
   <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="{{ url($settings->valueOf('company_logo')) }}">
   <!-- CSS here -->
   <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/swiper-bundle.css') }}">
   <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/flatpickr-min.css') }}">
   <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/magnific-popup.css') }}">
   <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/fontawesome-min.css') }}">
   <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/spacing.css') }}">
   <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/main.css') }}">
   @yield('css')
</head>

<body>

   <!-- pre loader area start -->
   <div id="loading">
      <div id="loading-center">
         <div id="loading-center-absolute">
            <!-- loading content here -->
            <div class="at-preloader-content">
               <div class="at-preloader-logo">
                  <div class="at-preloader-circle">
                     <svg width="190" height="190" viewBox="0 0 380 380" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle stroke="#D9D9D9" cx="190" cy="190" r="180" stroke-width="6" stroke-linecap="round">
                        </circle>
                        <circle stroke="red" cx="190" cy="190" r="180" stroke-width="6" stroke-linecap="round"></circle>
                     </svg>
                  </div>
                  <img src="{{ url($settings->valueOf('company_logo')) }}" alt="">
               </div>
               {{-- <p class="at-preloader-subtitle">Loading...</p> --}}
            </div>
         </div>
      </div>
   </div>
   <!-- pre loader area end -->

   <!-- back to top start -->
   <div class="back-to-top-wrapper">
      <button id="back_to_top" type="button" class="back-to-top-btn">
         <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 6L6 1L1 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
               stroke-linejoin="round" />
         </svg>
      </button>
   </div>
   <!-- back to top end -->

   <!-- start offcanvas area  -->
   <div class="at-offcanvas">
      <div class="at-offcanvas-header d-flex justify-content-between align-items-center mb-50">
         <div class="at-offcanvas-logo">
            <a href="{{ route('home') }}"><img src="{{ url($settings->valueOf('company_logo')) }}" alt=""></a>
         </div>
         <div class="at-offcanvas-close">
            <button class="at-offcanvas-close-toggle"><i class="fa-solid fa-xmark"></i></button>
         </div>
      </div>
      <div class="at-offcanvas-wrapper">
         <div class="at-offcanvas-menu mb-50 d-xl-none">
            <nav></nav>
         </div>
         <div class="at-offcanvas-dsc mb-40">
            <h3 class="at-offcanvas-title mb-10">La.Revi Hotel And Restaurant</h3>
            <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</p>
         </div>
         <div class="at-offcanvas-info mb-40 d-none d-xl-block">
            <h3 class="at-offcanvas-title mb-20">Contact Information</h3>
            <div class="at-offcanvas-info-content">
               <a href="tel:+123440567899">
                  <div class="at-offcanvas-info-item d-flex align-items-center">
                     <div class="at-offcanvas-info-icon mr-15">
                        <span><i class="fa-solid fa-phone"></i></span>
                     </div>
                     <div class="at-offcanvas-info-text">
                        <span>Call Us:</span>
                        <span>+12 344 0567899</span>
                     </div>
                  </div>
               </a>
               <a href="mailto:info@example.com">
                  <div class="at-offcanvas-info-item d-flex align-items-center">
                     <div class="at-offcanvas-info-icon mr-15">
                        <span><i class="fa-solid fa-envelope"></i></span>
                     </div>
                     <div class="at-offcanvas-info-text">
                        <span>Mail Us:</span>
                        <span>info@example.com</span>
                     </div>
                  </div>
               </a>
               <a href="https://maps.app.goo.gl/nzbF862fBa5UKqqk7" target="_blank">
                  <div class="at-offcanvas-info-item d-flex align-items-center">
                     <div class="at-offcanvas-info-icon mr-15">
                        <span><i class="fa-solid fa-location-dot"></i></span>
                     </div>
                     <div class="at-offcanvas-info-text at-location">
                        <span>Address:</span>
                        <span>71 Madison Ave, New York, NY 10016</span>
                     </div>
                  </div>
               </a>
            </div>
         </div>
         <div class="at-offcanvas-content mb-50">
            <h2 class="at-offcanvas-title mb-20">Image Gallery</h2>
            <div class="at-offcanvas-gallery">
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-1.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-1.jpg') }}" alt="">
               </a>
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-2.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-2.jpg') }}" alt="">
               </a>
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-3.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-3.jpg') }}" alt="">
               </a>
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-4.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-4.jpg') }}" alt="">
               </a>
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-5.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-5.jpg') }}" alt="">
               </a>
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-6.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-6.jpg') }}" alt="">
               </a>
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-7.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-7.jpg') }}" alt="">
               </a>
               <a class="popup-image" href="{{ asset('frontend_assets/assets/img/offcanvas/offcan-big-8.jpg') }}">
                  <img src="{{ asset('frontend_assets/assets/img/offcanvas/offcan-sm-8.jpg') }}" alt="">
               </a>
            </div>
         </div>
         <div class="at-offcanvas-social mb-50">
            <h3 class="at-offcanvas-title">Follow Us ON</h3>
            <a href="{{ $settings->valueOf('facebook') }}"><i class="fab fa-facebook-f"></i></a>
            <a href="{{ $settings->valueOf('twitter') }}">
               <span>
                  <svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path
                        d="M13.1236 0H15.6697L10.0793 6.36531L16.6107 15H11.4852L7.47233 9.75277L2.87823 15H0.332103L6.25461 8.19188L0 0H5.25277L8.87823 4.79336L13.1236 0ZM12.2325 13.5055H13.6439L4.51107 1.43911H2.99447L12.2325 13.5055Z"
                        fill="currentColor" />
                  </svg>
               </span>
            </a>
            <a href="https://discord.com/login"><i class="fa-brands fa-discord"></i></a>
            <a href="{{ $settings->valueOf('youtube') }}"><i class="fab fa-youtube"></i></a>
         </div>
      </div>
   </div>
   <!-- start offcanvas end  -->

   <!-- body overlay here -->
   <div class="body-overlay"></div>
   <!-- body overlay end here -->

   <header>

      <!-- header area start -->
      <div class="at-header-area at-header-transparent at-header-plr">
         <div class="container-fluid">
            <div class="at-header-top d-none d-lg-block">
               <div class="row justify-content-center gx-0">
                  <div class="col-xl-5 col-lg-5">
                     <div class="at-header-info d-flex align-items-center">
                        <div class="at-header-info-item">
                           <div class="at-header-top-lang-wrapper d-flex align-items-center">
                              <div class="at-header-lang d-none d-md-block">
                                 <span id="at-header-lang-toggle"
                                    class="at-header-lang-selected-lang at-lang-toggle">English <i class="fa-solid fa-angle-down"></i></span>
                                 <ul class="at-header-lang-list at-lang-list">
                                    <li>Spanish</li>
                                    <li>English</li>
                                    <li>Canada</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                        <div class="at-header-info-item d-none d-xl-block">
                           <div class="at-header-info-date">
                              <p class="at-header-info-text">Mon - Thu: <span>11am - 11pm</span></p>
                           </div>
                        </div>
                        <div class="at-header-info-item">
                           <div class="at-header-info-social d-flex align-items-center">
                              <p class="at-header-info-text mr-10">Follow Us:</p>
                              <div class="at-header-info-social-list">
                                 <ul>
                                    <li><a href="https://www.facebook.com/"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="https://www.x.com/"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="https://www.behance.com/"><i class="fa-brands fa-behance"></i></a></li>
                                    <li><a href="https://www.youtube.com/"><i class="fa-brands fa-youtube"></i></a></li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-2 col-lg-1">
                     <div class="at-header-logo mt-15 text-center">
                        <a href="{{ route('home') }}"><img src="{{ url($settings->valueOf('company_logo')) }}" style="width: 100px;" alt=""></a>
                     </div>
                  </div>
                  <div class="col-xl-5 col-lg-6">
                     <div class="at-header-info info-right d-flex align-items-center justify-content-end">
                        <div class="at-header-info-item d-none d-xl-block">
                           <div class="at-header-info-right">
                              <a href="tel:{{ ($settings->valueOf('phone')) }}"><span>P.</span>{{ ($settings->valueOf('phone')) }}</a>
                           </div>
                        </div>
                        <div class="at-header-info-item">
                           <div class="at-header-info-right">
                              <a href="#"><span>L.</span>{{ ($settings->valueOf('address')) }}</a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="header-sticky" class="at-header-bottom sticky1 text-lg-center">
               <div class="row align-items-center justify-content-center">
                  <div class="col-xxl-3 col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6">
                     <div class="at-header-logo text-start text-xl-center">
                        <a href="{{ route('home') }}">
                           <img src="{{ url($settings->valueOf('company_logo')) }}" style="width: 60px;" alt="">
                        </a>
                     </div>
                  </div>
                  <div class="col-xxl-6 col-xl-7 col-lg-7 d-none d-lg-block">
                     <div class="at-main-menu at-main-menu">
                        <nav class="at-mobile-menu-active">
                           <ul>
                              {{-- <li class="has-dropdown"><a href="#">Home <i class="fa-solid fa-angle-down"></i></a>
                                 <ul class="at-submenu submenu">
                                    <li><a href="index.h">Home-1</a></li>
                                    <li><a href="index-2.html">Home-2</a></li>
                                 </ul>
                              </li> --}}
                              <li><a href="menu.html">menus</a></li>
                              {{-- <li>
                                 <a href="#">News <i class="fa-solid fa-angle-down"></i></a>
                                 <ul class="at-submenu submenu">
                                    <li><a href="blog.html">Blog</a></li>
                                    <li><a href="blog-details.html">Blog Details</a></li>
                                 </ul>
                              </li> --}}
                              <li><a href="{{ route('contact') }}">Contact</a></li>
                           </ul>
                        </nav>
                     </div>
                  </div>
                  <div class="col-xxl-3 col-xl-3 col-lg-2 col-md-6 col-sm-6 col-6">
                     <div class="at-header-right-toggle text-end text-xl-center">
                        <button class="at-offcanvas-toogle at-header-right-bars">
                           <span>
                              <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <circle cx="26.25" cy="26.25" r="3.25" stroke="#B58C67" />
                                 <circle cx="15" cy="26.25" r="3.25" stroke="#B58C67" />
                                 <circle cx="3.75" cy="26.25" r="3.25" fill="#B58C67" stroke="#B58C67" />
                                 <circle cx="26.25" cy="15" r="3.25" stroke="#B58C67" />
                                 <circle cx="15" cy="15" r="3.25" fill="#B58C67" stroke="#B58C67" />
                                 <circle cx="3.75" cy="15" r="3.25" stroke="#B58C67" />
                                 <circle cx="26.25" cy="3.75" r="3.25" fill="#B58C67" stroke="#B58C67" />
                                 <circle cx="15" cy="3.75" r="3.25" stroke="#B58C67" />
                                 <circle cx="3.75" cy="3.75" r="3.25" stroke="#B58C67" />
                              </svg>
                           </span>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- header area end -->

   </header>

   <div id="smooth-wrapper">
      <div id="smooth-content">

        @yield('content')

         <footer>

            <!-- footer area start -->
            <section class="at-footer-area at-footer pt-80 pb-100">
               <div class="container">
                  <div class="row align-items-center">
                     <div class="col-xl-2 col-lg-6 col-md-6 d-xl-flex justify-content-start">
                        <div class="at-footer-widget text-xl-center footer-col-1">
                           <span class="at-footer-top-shape"></span>
                           <h3 class="at-footer-widget-title mb-40">get in touch</h3>
                           <div class="at-footer-contact">
                              <a href="tel:{{ ($settings->valueOf('phone')) }}"><span>T. </span>{{ ($settings->valueOf('phone')) }}</a>
                              <a href="mailto:{{ ($settings->valueOf('email')) }}"><span>M. </span>{{ ($settings->valueOf('email')) }}</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-xl-4 col-lg-6 col-md-6 d-xl-flex justify-content-center">
                        <div class="at-footer-widget footer-col-2 text-xl-center">
                           <span class="at-footer-top-shape"></span>
                           <h3 class="at-footer-widget-title mb-40">Address</h3>
                           <div class="at-footer-location">
                              <a href="https://maps.app.goo.gl/GyMHMQEYfdH8a9ji7">{{ $settings->valueOf('address') }}</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-xl-3 col-lg-6 col-md-6 d-xl-flex justify-content-center">
                        <div class="at-footer-widget text-xl-center footer-col-3">
                           <span class="at-footer-top-shape"></span>
                           <h3 class="at-footer-widget-title mb-40">Sub Branch</h3>
                           <div class="at-footer-location">
                              <a href="https://maps.app.goo.gl/GyMHMQEYfdH8a9ji7">{{ $settings->valueOf('address') }}</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-xl-3 col-lg-6 col-md-6 d-xl-flex justify-content-end">
                        <div class="at-footer-widget text-xl-center footer-col-4">
                           <span class="at-footer-top-shape"></span>
                           <h3 class="at-footer-widget-title mb-40">Opening hours</h3>
                           <div class="at-about-opening">
                              <p>Mon – Thu: <span>10.00 am – 01:00 am</span></p>
                              <p>Fri – Sun: <span>10:00 am – 02:00 am</span></p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- footer area end -->

            <!-- copyright area start -->
            <div class="at-copyright-area at-copyright pb-20">
               <div class="container">
                  <div class="at-copyright-wrapp">
                     <div class="row justify-content-center">
                        <div class="col-xl-5">
                           <div class="at-copyright-text text-center text-xl-start">
                              <p>Copyright & design by <a href="mailto:info@gmail.com">©Sadiem</a> 2025, All rights reserved</p>
                           </div>
                        </div>
                        <div class="col-xl-2">
                           <div class="at-copyright-logo text-center">
                              <a href="{{ route('home') }}">
                                 <img src="{{ url($settings->valueOf('company_logo')) }}" style="width: 50px;" alt="">
                              </a>
                           </div>
                        </div>
                        <div class="col-xl-5">
                           <div class="at-copyright-menu text-center text-xl-end">
                              <a href="faq.html">Faq</a>
                              <a href="#">T & C</a>
                              <a href="{{ route('contact') }}">Contact</a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- copyright area end -->
            </div>

         </footer>
      </div>
   </div>


   <!-- JS here -->
   <script src="{{ asset('frontend_assets/assets/js/vendor/jquery.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/vendors.min.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/gsap.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/gsap-scroll-to-plugin.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/gsap-scroll-smoother.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/gsap-scroll-trigger.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/gsap-split-text.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/vanilla-tilt.min.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/wow.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/nice-select.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/purecounter.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/flatpickr.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/silder-initialize.js') }}"></script>
   <script src="{{ asset('frontend_assets/assets/js/main.js') }}"></script>
   @yield('js')
</body>

</html>