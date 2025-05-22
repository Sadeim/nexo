@extends('frontend.layout')

@section('content')
    <!-- Page Header Start -->
    <div class="page-header parallaxie" style="background-image: url('{{ $sections['works_section']?->image ? asset($sections['works_section']->image) : '' }}');">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<!-- Page Header Box Start -->
					<div class="page-header-box">
						<h1 class="text-anime-style-3" data-cursor="-opaque">Our projects</h1>
						<nav class="wow fadeInUp">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="./">home</a></li>
								<li class="breadcrumb-item active" aria-current="page">our project</li>
							</ol>
						</nav>
					</div>
					<!-- Page Header Box End -->
				</div>
			</div>
		</div>
	</div>
	<!-- Page Header End -->

    <!-- Page Project Start -->
<div class="page-project bg-radius-section">
    <div class="container">
        <div class="row">
            @foreach($works as $index => $work)
                <div class="col-lg-4 col-md-6">
                    <!-- Project Item Start -->
                    <div class="project-item wow fadeInUp" data-wow-delay="{{ $index * 0.2 }}s">
                        <!-- Project Image Start -->
                        <div class="project-image">
                            {{-- <a href="{{ route('works.show', $work->slug) }}" data-cursor-text="View"> --}}
                                <figure class="image-anime">
                                    <img src="{{ asset($work->image) }}" alt="{{ $work->title }}">
                                </figure>
                            {{-- </a> --}}
                        </div>
                        <!-- Project Image End -->
        
                        <!-- Project Content Start -->
                        <div class="project-content">
                            <!-- Project Title Start -->
                            <div class="project-title">
                                <h3>
                                    {{-- <a href="{{ route('works.show', $work->slug) }}"> --}}
                                        {{ $work->title }}
                                    {{-- </a> --}}
                                </h3>
                            </div>
                            <!-- Project Title End -->
        
                            <!-- Project Tag Start -->
                            <div class="project-tag">
                                @foreach(explode(',', $work->category) as $tag)
                                    <a href="#" class="btn-default btn-highlighted">
                                        {{ trim($tag) }}
                                    </a>
                                @endforeach
                            </div>
                            <!-- Project Tag End -->
                        </div>
                        <!-- Project Content End -->
                    </div>
                    <!-- Project Item End -->
                </div>
            @endforeach
        </div>
    </div>
</div>

    <!-- Page Project End -->
@endsection
