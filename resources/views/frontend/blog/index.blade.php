@extends('frontend.layout')
@section('css')
@endsection
@section('content')
    <!-- Page Header Start -->
	<div class="page-header parallaxie" style="background-image: url('{{ $section?->image ? asset($section->image) : asset($about->image1) }}');">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<!-- Page Header Box Start -->
					<div class="page-header-box">
						<h1 class="text-anime-style-3" data-cursor="-opaque">Latest blog</h1>
						<nav class="wow fadeInUp">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
								<li class="breadcrumb-item active" aria-current="page">latest blog</li>
							</ol>
						</nav>
					</div>
					<!-- Page Header Box End -->
				</div>
			</div>
		</div>
	</div>
	<!-- Page Header End -->

    <!-- Page Blog Start -->
     <div class="page-blog bg-radius-section">
        <div class="container">
            <div class="row">
                @forelse ($blogs as $blog)
                    <div class="col-lg-4 col-md-6">
                        <!-- Post Item Start -->
                        <div class="post-item wow fadeInUp">
                            <!-- Post Featured Image Start-->
                            <div class="post-featured-image">
                                <figure>
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="image-anime" data-cursor-text="View">
                                        <img src="{{ asset($blog->image) }}" alt="">
                                    </a>
                                </figure>
                            </div>
                            <!-- Post Featured Image End -->

                            <!-- Post Item Body Start -->
                            <div class="post-item-body">
                                <!-- Post Item Content Start -->
                                <div class="post-item-content">
                                    <h3><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h3>
                                </div>
                                <!-- Post Item Content End -->

                                <!-- Post Item Readmore Button Start-->
                                <div class="post-item-btn">
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="btn-default">read more</a>
                                </div>
                                <!-- Post Item Readmore Button End-->
                            </div>
                            <!-- Post Item Body End -->
                        </div>
                        <!-- Post Item End -->
                    </div>
                @empty
                    No blogs found
                @endforelse
                <div class="col-lg-12">
                    <!-- Page Pagination Start -->
                    <div class="page-pagination wow fadeInUp" data-wow-delay="1.2s">
                        <ul class="pagination">
                            <li><a href="#"><i class="fa-solid fa-angle-left"></i></a></li>
                            <li class="active"><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#"><i class="fa-solid fa-angle-right"></i></a></li>
                        </ul>
                    </div>
                    <!-- Page Pagination End -->
                </div>
            </div>
        </div>
     </div>
    <!-- Page Blog End -->

@endsection
@section('js')
 
@endsection
