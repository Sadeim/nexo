@extends('frontend.layout')
@section('css')
@endsection
@section('content')
    <!-- Page Header Start -->
	<div class="page-header parallaxie">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<!-- Page Header Box Start -->
					<div class="page-header-box header-title">
						<h1 class="text-anime-style-3" data-cursor="-opaque">{{ $blog->title }}</h1>
						<nav class="wow fadeInUp">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
								<li class="breadcrumb-item"><a href="blog.html">blog</a></li>
								<li class="breadcrumb-item active" aria-current="page">{{ $blog->title }}</li>
							</ol>
						</nav>
					</div>
					<!-- Page Header Box End -->
				</div>
			</div>
		</div>
	</div>
	<!-- Page Header End -->

    <!-- Page Single Post Start -->
    <div class="page-single-post bg-radius-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Post Featured Image Start -->
                    <div class="post-image">
                        <figure class="image-anime">
                            <img src="images/post-1.jpg" alt="">
                        </figure>
                    </div>
                    <!-- Post Featured Image Start -->

                    <!-- Post Single Content Start -->
                    <div class="post-content">
                        <!-- Post Entry Start -->
                        <div class="post-entry">
                            <p class="wow fadeInUp">Keep your home in top condition year-round with our seasonal maintenance tips. From preparing for harsh winters to refreshing your space in spring, our expert advice helps you tackle essential tasks with ease. Stay ahead of potential issues, save on costly repairs, and enjoy a comfortable, well-maintained home every season.</p>

                            <p class="wow fadeInUp" data-wow-delay="0.2s">In spring, focus on refreshing your home by cleaning gutters, checking for winter damage, and servicing HVAC systems. Summer is ideal for exterior work, like power washing, sealing decks, and inspecting roofing. As autumn approaches, prepare for colder months by insulating your home, clearing leaves, and maintaining your heating system. Winter calls for safeguarding your pipes, sealing drafts, and checking smoke detectors for safety.</p>
                            
                            <blockquote class="wow fadeInUp" data-wow-delay="0.4s">
                                <p>Keep your home in peak condition all year with expert seasonal maintenance tips. Prevent costly repairs, enhance energy efficiency, and ensure safety with simple, actionable advice tailored for spring, summer, fall, and winter care.</p>
                            </blockquote>

                            <p class="wow fadeInUp" data-wow-delay="0.6s">With these practical tips, you can avoid costly repairs, enhance your home's efficiency, and create a comfortable environment for your family year-round. Stay ahead of the seasons with a maintenance routine designed to protect your investment and simplify home care.</p>

                            <h2 class="wow fadeInUp" data-wow-delay="0.8s">Seasonal Home Care Essentials</h2>

                            <p class="wow fadeInUp" data-wow-delay="1s">Stay ahead of home maintenance with essential tips for every season. From spring cleaning to winterizing, ensure your home remains safe, efficient, and comfortable year-round with simple, proactive care.</p>

                            <ul class="wow fadeInUp" data-wow-delay="1.2s">
                                <li>Spring Cleaning and Maintenance Checklist for a Fresh Start</li>
                                <li>Summer Home Care: Keep Your Home Cool and Comfortable</li>
                                <li>Year-Round Home Maintenance: Seasonal Tasks You Can't Miss</li>
                                <li>Seasonal Inspections: Why Every Home Needs Regular Check-ups</li>
                                <li>Spring to Winter: The Ultimate Seasonal Maintenance Guide</li>
                            </ul>

                            <p class="wow fadeInUp" data-wow-delay="1.4s">Essential tips for maintaining your home year-round. From spring cleaning to winter preparation, ensure your home stays in top shape, enhancing comfort, safety.</p>
                        </div>
                        <!-- Post Entry End -->

                        <!-- Post Tag Links Start -->
                        <div class="post-tag-links">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <!-- Post Tags Start -->
                                    <div class="post-tags wow fadeInUp" data-wow-delay="0.5s">
                                        <span class="tag-links">
                                            Tags:
                                            <a href="#">repairs</a>
                                            <a href="#">handyman</a>
                                            <a href="#">quick fixes</a>
                                        </span>
                                    </div>
                                    <!-- Post Tags End -->
                                </div>

                                <div class="col-lg-4">
                                    <!-- Post Social Links Start -->
                                    <div class="post-social-sharing wow fadeInUp" data-wow-delay="0.5s">
                                        <ul>
                                            <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                                            <li><a href="#"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                            <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                                            <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                                        </ul>
                                    </div>
                                    <!-- Post Social Links End -->
                                </div>
                            </div>
                        </div>
                        <!-- Post Tag Links End -->
                    </div>
                    <!-- Post Single Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Single Post End -->

@endsection
@section('js')
  
@endsection
