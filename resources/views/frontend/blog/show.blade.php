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
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($blog->title, 30) }}</li>
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
                        <img src="{{ asset('uploads/blogs/' . $blog->image) }}" alt="{{ $blog->title }}">
                    </figure>
                </div>
                <!-- Post Featured Image End -->

                <!-- Post Single Content Start -->
                <div class="post-content">
                    <!-- Post Entry Start -->
                    <div class="post-entry">
                        {!! $blog->content !!}
                    </div>
                    <!-- Post Entry End -->

                    <!-- Post Tag Links Start -->
                    <div class="post-tag-links">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <!-- Post Tags Start -->
                                <div class="post-tags wow fadeInUp" data-wow-delay="0.5s">
                                    <span class="tag-links">
                                        Category:
                                        <a href="#">{{ $blog->category }}</a>
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
