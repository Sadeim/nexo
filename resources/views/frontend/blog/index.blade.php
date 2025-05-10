@extends('frontend.layout')
@section('css')
@endsection
@section('content')
<div class="blogs-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="row blogs-pr" id="search-results">
                    @forelse ($blogs as $blog)
                        <div class="col-lg-12">
                            <div class="single-blog-box">
                                <div class="blog-thumb">
                                    <img src="{{ asset($blog->image) }}" alt="">
                                    <div class="meta-blog">
                                        <a href="{{ route('blog.show', $blog->slug) }}"> {{ strtoupper(\Carbon\Carbon::parse($blog->created_at)->format('d M, Y')) }}</a>
                                    </div>
                                </div>
                                <div class="blog-content">
                                    <h2 class="blog-title"><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h2>
                                    <p class="blog-desc">{{ $blog->content }}</p>
                                    <ul class="blog-author">
                                        <li> <i class="far fa-user"></i> <span>By {{ $blog->author }}</span> </li>
                                        {{-- <li> <i class="bi bi-chat"></i> {{ $blog->comments->count() }} Comments </li> --}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        No blogs
                    @endforelse
                </div>
            </div>
            <div class="col-lg-3 col-md-12 pl-0 pr-0 responsive">
                <div class="sidebar-widget">
                    <form action="#" method="get">
                        <input type="text" class="src-input-box" placeholder="Search Here" name="s" value=""
                            title="src-input-box">
                        <button class="subscribe-btn" type="submit">
                            <span><i class="bi bi-search"></i></span>
                        </button>
                    </form>
                </div>
                <div class="widget-sidebar-box">
                    <h4 class="sidebar-title">Categories</h4>
                    <ul class="sidebar-menu">
                        @forelse ($categories as $category)
                            <li><a href="#"> <i class="bi bi-check-lg"></i> {{ $category->name }} </a></li>                            
                        @empty
                            No categories
                        @endforelse
                    </ul>
                </div>
                <div class="widget-sidebar-box">
                    <h4 class="sidebar-title upp"> Popular Post </h4>
                    @forelse ($popular_posts as $popular_post)
                        <div class="widget-recent-post d-flex">
                            <div class="rpost-thumb">
                                <a href="{{ route('blog.show', $popular_post->slug) }}"><img src="{{ asset($popular_post->image) }}" alt="post thumb"></a>
                            </div>
                            <div class="rpost-content">
                                <div class="rpost-title">
                                    <h4><a href="{{ route('blog.show', $popular_post->slug) }}">{{ $popular_post->title }}</a></h4>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="widget-recent-post d-flex">
                            No Popular Post 
                        </div>
                    @endforelse
                </div>
                <!-- categoreis thumb -->
                <div class="widget-sidebar-quick-contact">
                    <div class="widget-categories-content text-center">
                        <div class="logo-thumb">
                            <a href="{{ route('home') }}"> <img src="{{ asset('frontend_assets/assets/images/logo.png') }}" alt=""> </a>
                        </div>
                        <h3 class="widget-title2" >NEED HELP?</h3>
                        <h5 class="sidebar-title">Call Us</h5>
                        <h5 class="sidebar-title2">$settings->valueOf('company_logo')</h5>
                        <div class="widget-button">
                            <a href="{{ route('contact') }}"> Contact Us </a>
                        </div>
                    </div>
                </div>
                <div class="widget-sidebar-box upp">
                    <h4 class="sidebar-title upp">Categories</h4>
                    <div class="tag-item">
                        <ul>
                            <li><a href="#">Repair</a></li>
                            <li><a href="#">Handyman</a></li>
                            <li><a href="#">Roop</a></li>
                            <li><a href="#">Door</a></li>
                            <li><a class="item1" href="#">Floor</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        $('form').on('submit', function (e) {
            e.preventDefault();
    
            const query = $('input[name="s"]').val().trim();
            if (query === '') return;
    
            $.ajax({
                url: "{{ route('blog.search') }}",
                type: "GET",
                data: { q: query },
                beforeSend: function () {
                    $('#search-results').html('<p>Loading...</p>');
                },
                success: function (response) {
                    $('#search-results').html(response);
                },
                error: function () {
                    $('#search-results').html('<p class="text-danger">An error occurred. Try again later.</p>');
                }
            });
        });
    });
</script>    
@endsection
