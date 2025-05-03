@extends('frontend.layout')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection
@section('content')
	<div class="hero-list owl-carousel">
		@foreach ($sliders as $slider)
			<div class="hero-section d-flex align-items-center">
				<div class="container">
					<div class="row align-items-center">
						<div class="col-lg-6 col-md-6">
							<div class="sero-content">
								<h4>{{ $slider->title }}</h4>
								<div class="hero-button">
									<a href="{{ $slider->button_link }}"> {{ $slider->button_text }} <i class="bi bi-plus"></i></a>
								</div>
								<div class="hero-shape">
									<img src="{{ $slider->image }}" alt="">
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="hero-thumb">
								<img src="{{ $slider->image }}" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>

	<div class="feature-section">
		<div class="container-fluid">
			<div class="row feature-bg align-items-center">
				<div class="col-lg-8 col-md-6">
					<div class="hendre-section-title padding-lg">
						<h4>features</h4>
						<h1>Fixing What We <span>Improves</span></h1>
					</div>
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="feature-contact-info">
						<div class="feature-ctn-icon">
							<img src="{{ asset('frontend_assets/assets/images/resource/icon.png') }}" alt="">
						</div>
						<div class="feature-contact">
							<span class="feature-ask">For Enquery :</span>
							<h2 class="feature-phone-number">{{ $settings->valueOf('phone') }}</h2>
						</div>
					</div>
				</div>
				@foreach ($features as $feature)
					<div class="col-lg-4 col-md-6">
						<div class="feature-single-box">
							<div class="feature-thumb">
								<img src="{{ asset($feature->image) }}" alt="">
								{{-- <div class="feature-icon">
									<img src="assets/images/resource/feature1.png" alt="">
									<a class="feature-icon2" href="service-details.html"><i class="bi bi-arrow-right"></i></a>
								</div> --}}
								<div class="feature-content">
									<h2>{{ $feature->title }}</h2>
								</div>
							</div>
						</div>
					</div>
				@endforeach

				<div class="feature-shape">
					<img src="assets/images/resource/feature-shape.jpg" alt="">
				</div>
			</div>
		</div>
	</div>

	<div class="about-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6 col-md-12">
					<div class="about-right-thumb">
						<img src="{{ asset($about->image1) }}" alt="">
						<div class="about-counter">
							<h2 class="counter">795</h2>
							<h2 class="counter1">+</h2>
							<span class="counter-text">Project Completed</span>
						</div>
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
	
	<div class="service-top-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12">
					<div class="hendre-section-title white padding-lg">
						<h4>OUR SERVICES</h4>
						<h1>Solutions for Renovating</h1>
						<h1 class="sections">Home <span>Repairing</span></h1>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="service-section">
		<div class="container">
			<div class="row service-bg">
				<div class="service-list owl-carousel">
					@foreach($services as $service)
						<div class="col-lg-12">
							<div class="single-service-box">
								<div class="service-thumb">
									<img src="{{ asset($service->image) }}" alt="">
								</div>
								<div class="service-content">
									<div class="service-icon">
										<img src="{{ asset($service->icon) }}" alt="">
									</div>
									<h3 class="service-title">{{ $service->name }}</h3>
									<p class="service-desc">{{ $service->description }}</p>
									<a class="hendre-button" href="route('service.details', $service->id)">
										Read More <i class="bi bi-plus"></i>
									</a>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	
	<div class="why-choose-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12">
					<div class="hendre-section-title text-center padding-lg">
						<h4>{{ $sectionTitle ?? 'Why choose us' }}</h4>
						<h1>Some Reason for Choose <span> HRS </span></h1>
						<h1 class="sections">Repairing Your Home</h1>
						<div class="rs-video2">
							<div class="animate-border">
								<a class="video-vemo-icon venobox vbox-item" data-vbtype="youtube" data-autoplay="true"
									href="{{ $videoUrl ?? 'https://youtu.be/BS4TUd7FJSg' }}">
									Play
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
	
			@if($tabs->count())
			<div class="row">
				<div class="col-lg-12">
					<div class="tab">
						<ul class="tabs">
							@foreach($tabs as $index => $tab)
							<li><a href="#"><span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}.</span> {{ $tab->title }}</a></li>
							@endforeach
						</ul>
	
						<div class="tab_content">
							@foreach($tabs as $tab)
							<div class="tabs_item">
								<div class="row">
									<div class="col-lg-6 col-md-6">
										<div class="tab-thumb">
											<img src="{{ asset($tab->image) }}" alt="">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 tab-right">
										<div class="hendre-section-title">
											<h4>{{ $tab->title }}</h4>
											<h1>{!! $tab->subtitle !!}</h1>
											<h1 class="sections">Looks as a New Home</h1>
											<p>{{ $tab->description }}</p>
										</div>
	
										<div class="row">
											@foreach(array_chunk($tab->features, ceil(count($tab->features)/2)) as $featureColumn)
											<div class="col-lg-6 col-md-6">
												<div class="about-item-list">
													<ul>
														@foreach($featureColumn as $feature)
														<li><i class="bi bi-check-circle-fill"></i> {{ $feature }}</li>
														@endforeach
													</ul>
												</div>
											</div>
											@endforeach
										</div>
	
										<div class="hendre-button">
											<a href="route('service.details')">Get An Estimate <i class="bi bi-plus"></i></a>
										</div>
									</div>
								</div>
							</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
	

	<div class="team-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-md-12">
					<div class="hendre-section-title white">
						<h4> Our Team </h4>
						<h1> Meet Our Experts </h1>
						<h1 class="sections"> Team <span>Member</span> </h1>
						<p>Competently repurpose go forward benefits without goal-oriented ROI the conveniently target business opportunities whereas proactive</p>
					</div>
				</div>
				<div class="col-lg-8 col-md-12">
					<div class="row">
						<div class="team-list owl-carousel">
							@foreach ($teams as $team)
								<div class="col-lg-12">
									<div class="single-team-box">
										<div class="team-thumb">
											<img src="{{ asset($team->image) }}" alt="">

											<ul class="team-social-list">
												@foreach(json_decode($team->social_links, true) as $platform => $link)
													<li><a href="{{ $link }}"><i class="fab fa-{{ $platform }}"></i></a></li>
												@endforeach
												{{-- <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
												<li><a href="#"><i class="fab fa-twitter"></i></a></li>
												<li><a href="#"><i class="fab fa-linkedin-in"></i></a></li> --}}
											</ul>
										</div>
										<div class="team-content">
											<h3 class="team-title">{{ $team->name }}</h3>
											<p class="team-text">{{ $team->position }}</p>
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

	<div class="testimonial-section">
		<div class="container">
			<div class="row testi-bg">
				<div class="col-lg-5 col-md-12">
					<div class="row">
						<div class="testmn-bg">
							<div class="testi-list owl-carousel">
								<div class="col-lg-12">
									@foreach($testimonials as $testimonial)
										<div class="teastimonial-single-box">
											<div class="testi-content">
												<div class="testi-icon"><i class="bi bi-quote"></i></div>
												<p class="testi-desc">{{ $testimonial->comment }}</p>
												<div class="testi-rating">
													@for($i = 0; $i < $testimonial->rating; $i++)
														<i class="bi bi-star-fill"></i>
													@endfor
												</div>
												<div class="user-pic">
													<img src="{{ asset('storage/'.$testimonial->image) }}" alt="">
												</div>
												<div class="user-name">
													<h4>{{ $testimonial->name }}</h4>
													<span>{{ $testimonial->position }}</span>
												</div>
											</div>
										</div>
									@endforeach

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-7 col-md-12">
					<!-- contact form box -->
					<div class="contact-form-box">

						<div class="hendre-section-title pb-tsmn">
							<h4> BOOKING NOW </h4>
							<h1> Booking A <span>Services</span> </h1>
						</div>
						<form action="{{ route('contact.submit') }}" method="POST" id="dreamit-form">
							@csrf
							<div class="row">
								<div class="col-lg-6 col-md-6">
									<div class="form-box">
										<input type="text" name="name" placeholder="Your Name*" required>
									</div>
								</div>
								<div class="col-lg-6 col-md-6">
									<div class="form-box">
										<input type="email" name="email" placeholder="Enter E-Mail" required>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-box">
										<input type="text" name="phone" placeholder="Mobile No." required>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-box">
										<select name="subject" required>
											<option value="">Select Service*</option>
											<option value="Electrical System">Electrical System</option>
											<option value="Auto Car Repair">Auto Car Repair</option>
											<option value="Engine Diagnostics">Engine Diagnostics</option>
											<option value="Car & Engine Clean">Car & Engine Clean</option>
										</select>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-box">
										<textarea name="message" cols="30" rows="10" placeholder="Write Message:" required></textarea>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="contact-form">
										<button type="submit">Submit Request</button>
									</div>
								</div>
							</div>
						</form>
						
						<div id="status"></div>
					</div> 
				</div>
			</div>
		</div>
	</div>

	<div class="process-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12">
					<div class="hendre-section-title white text-center padding-lg">
						<h4> Work Process </h4>
						<h1> We Follow the <span>Processr</span> </h1>
					</div>
				</div>
			</div>
			<div class="row">
				@foreach ($works as $key => $work)
					<div class="col-lg-3 col-md-6">
						<div class="single-process-box">
							<div class="process-thumb">
								<img src="{{ asset($work->image) }}" alt="">
								<div class="process-number">
									<span>0{{ $key + 1 }}</span>
								</div>
							</div>
							<div class="process-content">
								<h4 class="process-title">{{ $work->title }}</h4>
								<p class="process-desc">{{ $work->description }}</p>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endsection

@section('js')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}
<script>
	$(document).ready(function() {
		$('#dreamit-form').on('submit', function(e) {
			e.preventDefault();
	
			let form = $(this);
			let formData = form.serialize();
	
			// Validation manually
			let name = $('input[name="name"]').val().trim();
			let email = $('input[name="email"]').val().trim();
			let mobile = $('input[name="phone"]').val().trim();
			let service = $('select[name="subject"]').val();
			let message = $('textarea[name="message"]').val().trim();
	
			if (name === '' || email === '' || mobile === '' || service === '' || message === '') {
				toastr.error('Please fill in all fields.');
				return;
			}
	
			$.ajax({
				url: form.attr('action'),
				method: 'POST',
				data: formData,
				success: function(response) {
					toastr.success(response.message);
					form[0].reset();
				},
				error: function(xhr) {
					let errors = xhr.responseJSON.errors;
					for (const key in errors) {
						toastr.error(errors[key][0]);
					}
				}
			});
		});
	});
</script>


@endsection
