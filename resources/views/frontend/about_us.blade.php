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

    <!-- Our Approch Section Start -->
    <div class="our-approch bg-radius-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Our Approch Image Start -->
                    <div class="our-approch-image">
                        <div class="our-approch-img-1">
                            <figure class="image-anime">
                                <img src="images/our-approch-img-1.jpg" alt="">
                            </figure>
                        </div>

                        <div class="our-approch-img-2">
                            <figure class="image-anime">
                                <img src="images/our-approch-img-2.jpg" alt="">
                            </figure>
                        </div>
                    </div>
                    <!-- Our Approch Image End -->
                </div>
                
                <div class="col-lg-6">
                    <!-- Our Approch Content Start -->
                    <div class="our-approch-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">our approach</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Handyman services with personal touch</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- Our Approch Tab Start -->
                        <div class="our-approch-tab">
                            <!-- Sidebar Our Approch Nav start -->
                            <div class="our-approch-tab-nav wow fadeInUp" data-wow-delay="0.2s">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn-default btn-highlighted active" id="mission-tab" data-bs-toggle="tab" data-bs-target="#mission" type="button" role="tab" aria-selected="true">our mission</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn-default btn-highlighted" id="vision-tab" data-bs-toggle="tab" data-bs-target="#vision" type="button" role="tab" aria-selected="false">our vision</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn-default btn-highlighted" id="value-tab" data-bs-toggle="tab" data-bs-target="#value" type="button" role="tab" aria-selected="false">our value</button>
                                    </li>
                                </ul>
                            </div>
                            <!-- Sidebar Our Approch Nav End -->

                            <!-- Approch Box Start -->
                            <div class="approch-box tab-content" id="myTabContent">
                                <!-- Approch Item Start -->
                                <div class="approch-item tab-pane fade show active" id="mission" role="tabpanel">
                                    <div class="row align-items-center">
                                        <div class="col-lg-12">
                                            <!-- Approch Tab Content Start -->
                                            <div class="approch-tab-content">
                                                <div class="approch-tab-content-header">
                                                    <p>Our mission is to provide reliable, high-quality handyman services that enhance homes and simplify lives, delivering craftsmanship with integrity and care.</p>
                                                </div>

                                                <div class="approch-tab-content-list">
                                                    <ul>
                                                        <li>dependable repairs, every time</li>
                                                        <li>improving homes, enhancing lives</li>
                                                        <li>customer-centered approach</li>
                                                    </ul>
                                                </div>                                    
                                            </div>
                                            <!-- Approch Tab Content End -->
                                        </div>
                                    </div>
                                </div>
                                <!-- Approch Item End -->

                                <!-- Approch Item Start -->
                                <div class="approch-item tab-pane fade" id="vision" role="tabpanel">
                                    <div class="row align-items-center">
                                        <div class="col-lg-12">
                                            <!-- Approch Tab Content Start -->
                                            <div class="approch-tab-content">
                                                <div class="approch-tab-content-header">
                                                    <p>Our vision is to provide reliable, high-quality handyman services that enhance homes and simplify lives, delivering craftsmanship with integrity and care.</p>
                                                </div>

                                                <div class="approch-tab-content-list">
                                                    <ul>
                                                        <li>dependable repairs, every time</li>
                                                        <li>improving homes, enhancing lives</li>
                                                        <li>customer-centered approach</li>
                                                    </ul>
                                                </div>                                    
                                            </div>
                                            <!-- Approch Tab Content End -->
                                        </div>
                                    </div>
                                </div>
                                <!-- Approch Item End -->

                                <!-- Approch Item Start -->
                                <div class="approch-item tab-pane fade" id="value" role="tabpanel">
                                    <div class="row align-items-center">
                                        <div class="col-lg-12">
                                            <!-- Approch Tab Content Start -->
                                            <div class="approch-tab-content">
                                                <div class="approch-tab-content-header">
                                                    <p>Our value is to provide reliable, high-quality handyman services that enhance homes and simplify lives, delivering craftsmanship with integrity and care.</p>
                                                </div>

                                                <div class="approch-tab-content-list">
                                                    <ul>
                                                        <li>dependable repairs, every time</li>
                                                        <li>improving homes, enhancing lives</li>
                                                        <li>customer-centered approach</li>
                                                    </ul>
                                                </div>                                    
                                            </div>
                                            <!-- Approch Tab Content End -->
                                        </div>
                                    </div>
                                </div>
                                <!-- Approch Item End -->
                            </div>
                            <!-- Approch Box End -->
                        </div>
                        <!-- Our Approch Tab End -->
                    </div>
                    <!-- Our Approch Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Approch Section End -->

    <!-- Our Skill Section Start -->
    <div class="our-skill bg-radius-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="our-skill-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">our skill</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Handyman expertise for every task</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Our skilled professionals bring versatile expertise to handle a wide range of repairs, installations, and improvements, ensuring every task is completed with precision and care.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- About SkillBar Start -->
                        <div class="our-skillbars">
                            <!-- Skills Progress Bar Start -->
                            <div class="skills-progress-bar">
                                <!-- Skill Item Start -->
                                <div class="skillbar" data-percent="66%">
                                    <div class="skill-data">
                                        <div class="skill-title">carpentry</div>
                                        <div class="skill-no">66%</div>
                                    </div>
                                    <div class="skill-progress">
                                        <div class="count-bar"></div>
                                    </div>
                                </div>
                                <!-- Skill Item End -->
                            </div>
                            <!-- Skills Progress Bar End -->
                        
                            <!-- Skills Progress Bar Start -->
                            <div class="skills-progress-bar">
                                <!-- Skill Item Start -->
                                <div class="skillbar" data-percent="97%">
                                    <div class="skill-data">
                                        <div class="skill-title">plumbing repairs</div>
                                        <div class="skill-no">97%</div>
                                    </div>
                                    <div class="skill-progress">
                                        <div class="count-bar"></div>
                                    </div>
                                </div>
                                <!-- Skill Item End -->
                            </div>
                            <!-- Skills Progress Bar End -->
                            
                            <!-- Skills Progress Bar Start -->
                            <div class="skills-progress-bar">
                                <!-- Skill Item Start -->
                                <div class="skillbar" data-percent="85%">
                                    <div class="skill-data">
                                        <div class="skill-title">electrical maintenance</div>
                                        <div class="skill-no">85%</div>
                                    </div>
                                    <div class="skill-progress">
                                        <div class="count-bar"></div>
                                    </div>
                                </div>
                                <!-- Skill Item End -->
                            </div>
                            <!-- Skills Progress Bar End -->
                        </div>
                        <!-- About SkillBar End -->
                    </div>
                </div>

                <div class="col-lg-6">
                    <!-- Our Skill Image Start -->
                    <div class="our-skill-image">
                        <div class="our-skill-img-1">
                            <figure class="image-anime">
                                <img src="images/our-skill-img-1.jpg" alt="">
                            </figure>
                        </div>
                
                        <div class="our-skill-img-2">
                            <figure class="image-anime">
                                <img src="images/our-skill-img-2.jpg" alt="">
                            </figure>
                        </div>
                
                        <div class="our-skill-img-3">
                            <figure class="image-anime">
                                <img src="images/our-skill-img-3.jpg" alt="">
                            </figure>
                        </div>
                    </div>
                    <!-- Our Skill Image End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Skill Section End -->

    <!-- Quick Fact Section Start -->
    <div class="quick-facts bg-radius-section">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-6">
                    <!-- Section Title Start -->
                    <div class="section-title dark-section">
                        <h3 class="wow fadeInUp">Some facts</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Quick facts about our services</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
                
                <div class="col-lg-6">
                    <!-- Section Title Content Start -->
                    <div class="section-title-content dark-section wow fadeInUp" data-wow-delay="0.2s">
                        <p>From repairs to home improvements, our comprehensive handyman services cover everything you</p>
                    </div>
                    <!-- Section Title Content End -->
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6 col-6 order-lg-1 order-md-1 order-1">
                    <!-- Fact Counter Box Start -->
                    <div class="fact-counter-box">
                        <!-- Fact Counter Item Start -->
                        <div class="fact-counter-item">
                            <h3>experience</h3>
                            <h2><span class="counter">25</span>+</h2>
                            <p>years of experience</p>
                        </div>
                        <!-- Fact Counter Item End -->

                        <!-- Fact Counter Item Start -->
                        <div class="fact-counter-item">
                            <h3>people</h3>
                            <h2><span class="counter">320</span>k</h2>
                            <p>working staff</p>
                        </div>
                        <!-- Fact Counter Item End -->
                    </div>
                    <!-- Fact Counter Box End -->
                </div>
                
                <div class="col-lg-6 order-lg-2 order-md-3 order-3">
                    <!-- Quick Fact image Start -->
                    <div class="quick-fact-image">
                        <img src="images/quick-fact-img.png" alt="">
                    </div>
                    <!-- Quick Fact image End -->
                </div>
                
                <div class="col-lg-3 col-md-6 col-6 order-lg-3 order-md-2 order-2">
                    <!-- Fact Counter Box Start -->
                    <div class="fact-counter-box">
                        <!-- Fact Counter Item Start -->
                        <div class="fact-counter-item">
                            <h3>work</h3>
                            <h2><span class="counter">8</span>k+</h2>
                            <p>project complete</p>
                        </div>
                        <!-- Fact Counter Item End -->

                        <!-- Fact Counter Item Start -->
                        <div class="fact-counter-item">
                            <h3>client</h3>
                            <h2><span class="counter">100</span>%</h2>
                            <p>satisfaction guarante</p>
                        </div>
                        <!-- Fact Counter Item End -->
                    </div>
                    <!-- Fact Counter Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Fact Section End -->
    
    <!-- Why Choose Us Section Start -->
    <div class="why-choose-us">
        <div class="why-choose-box bg-radius-section">
            <div class="container">
                <div class="row section-row align-items-end">
                    <div class="col-lg-6">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">why choose us</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Showcasing our handyman projects</h2>
                        </div>
                        <!-- Section Title End -->
                    </div>

                    <div class="col-lg-6">
                        <!-- Why Choose Image Start -->
                        <div class="why-choose-image">
                            <figure class="image-anime">
                                <img src="images/why-choose-img.jpg" alt="">
                            </figure>
                        </div>
                        <!-- Why Choose Image End -->
                    </div>
                </div>

                <div class="row">    
                    <div class="col-lg-12">
                        <!-- Why Choose Project Box Start -->
                        <div class="why-choose-project-box">
                            <!-- Why Choose Project Item Start -->
                            <div class="why-choose-project-item wow fadeInUp">
                                <div class="icon-box">
                                    <img src="images/icon-service-1.svg" alt="">
                                </div>
    
                                <div class="why-choose-project-content">
                                    <h3>locally owned</h3>
                                    <p>As a local business, we care about our community and take pride in serving our.</p>
                                </div>
                            </div>
                            <!-- Why Choose Project Item End -->
    
                            <!-- Why Choose Project Item Start -->
                            <div class="why-choose-project-item wow fadeInUp" data-wow-delay="0.2s">
                                <div class="icon-box">
                                    <img src="images/icon-service-2.svg" alt="">
                                </div>
    
                                <div class="why-choose-project-content">
                                    <h3>on-time service</h3>
                                    <p>As a local business, we care about our community and take pride in serving our.</p>
                                </div>
                            </div>
                            <!-- Why Choose Project Item End -->
    
                            <!-- Why Choose Project Item Start -->
                            <div class="why-choose-project-item wow fadeInUp" data-wow-delay="0.4s">
                                <div class="icon-box">
                                    <img src="images/icon-service-3.svg" alt="">
                                </div>
    
                                <div class="why-choose-project-content">
                                    <h3>transparent pricing</h3>
                                    <p>As a local business, we care about our community and take pride in serving our.</p>
                                </div>
                            </div>
                            <!-- Why Choose Project Item End -->
    
                            <!-- Why Choose Project Item Start -->
                            <div class="why-choose-project-item wow fadeInUp" data-wow-delay="0.6s">
                                <div class="icon-box">
                                    <img src="images/icon-service-4.svg" alt="">
                                </div>
    
                                <div class="why-choose-project-content">
                                    <h3>quality assurance</h3>
                                    <p>As a local business, we care about our community and take pride in serving our.</p>
                                </div>
                            </div>
                            <!-- Why Choose Project Item End -->
                        </div>
                        <!-- Why Choose Project Box End -->
    
                        <!-- Why Choose Footer Start -->
                        <div class="why-choose-footer wow fadeInUp" data-wow-delay="0.8s">
                            <p>Our construction company is the perfect choice for your dream. <a href="contact.html">Contact us now today!</a></p>
                        </div>
                        <!-- Why Choose Footer End -->
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <!-- Why Choose Us Section End -->

@endsection
