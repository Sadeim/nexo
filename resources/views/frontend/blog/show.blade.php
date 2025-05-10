@extends('frontend.layout')
@section('css')
@endsection
@section('content')
<div class="blogs-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="row blogs-pr"  id="search-results">
                    <div class="col-lg-12">
                        <div class="single-blog-dtls-box">
                            <div class="blog-thumb">
                                <img src="{{ asset($blog->image) }}" alt="">
                                <div class="meta-blog">
                                    <a href="#">{{ strtoupper(\Carbon\Carbon::parse($blog->created_at)->format('d M, Y')) }}</a>
                                </div>
                            </div>
                            <div class="blog-content">

                                <ul class="blog-author">
                                    <li> <i class="far fa-user"></i> <span>By {{ $blog->author }}</span> </li>
                                    <li> <i class="bi bi-chat"></i> {{ strtoupper(\Carbon\Carbon::parse($blog->created_at)->format('d M, Y')) }} </li>
                                </ul>

                                <h2 class="blog-title"><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h2>

                                <p class="blog-desc2">{{ $blog->content }}</p>
                                {{-- <p>{{ $blog->content }}</p> --}}

                                {{-- <div class="blog-quote">
                                    <img class="qte-icon" src="assets/images/resource/quote-1.jpg" alt="icon">
                                    <p>Globally envisioneer intuitive paradigms whereas has innovation. sucking web-readines quality market competently.</p>
                                </div>

                                <h2 class="blog-title"><a href="blog-details.html">How to Home Repairing?</a></h2>

                                <p class="blog-desc2">Efficiently productivate standardized processes whereas sustainable expertise. Objectively negotiate exten leadership skills through B2C opportunities. Compellingly formulate viral functionalities before alternative Dynamically matrix strategic interfaces whereas</p>

                                <div class="row dtls-thmb">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="blog-dtls-thumb">
                                            <img src="assets/images/resource/dtls.jpg" alt="">
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6">
                                        <div class="blog-dtls-thumb">
                                            <img src="assets/images/resource/dtls2.jpg" alt="">
                                        </div>
                                    </div>
                                </div>

                                <h2 class="blog-title"><a href="blog-details.html">Home Repair Benifits</a></h2>

                                <p class="blog-desc2">Leadership skills through B2C opportunities. Compellingly formulate viral functionalities before alternative Efficiently productivate standardized processes whereas sustainable expertise.</p>

                                <ul class="product-list">
                                    <li> <i class="bi bi-check2"></i> Efficiently reintermediate pandemic infomediarie. </li>
                                    <li> <i class="bi bi-check2"></i> Driven technologies enthusiastically </li>
                                    <li> <i class="bi bi-check2"></i> Authoritatively target exceptional partnerships </li>
                                    <li> <i class="bi bi-check2"></i> Covalent testing procedures  intermandated </li>
                                    <li> <i class="bi bi-check2"></i> Dramatically facilitate intuitive niches whereas </li>
                                </ul> --}}
                            </div>

                            <div class="blogs-social-share">
                                <span class="social-text">Social Share :</span>
                                <ul class="social-share">
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="bi bi-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                </ul>
                            </div>
                        {{-- 
                            <div class="blog-post-comment">
                                <h3 class="blog-comment-title">Comments (02)</h3>
                                <!-- post comment -->
                                <div class="post-comment">

                                    <div class="post-comment-thumb">
                                        <a href="blog-details.html"><img src="assets/images/resource/comnt-pl.png" alt=""></a>
                                    </div>

                                    <div class="post-content">

                                        <h4 class="post-title">David Alexon <span class="left-date">October 01, 2022</span></h4>
                                        <p class="posts-reply"> Dramatically reinvent adaptive bandwidth vis reliable infrastructures to the progressively are distributed interfaces have a nice day with our new car. <span class="rights-reply"> <i class="bi bi-reply-fill"></i>Reply</span></p>

                                    </div>
                                </div>

                                <div class="post-comment2">

                                    <div class="post-comment-thumb">
                                        <a href="blog-details.html"><img src="assets/images/resource/comnt-pl.png" alt=""></a>
                                    </div>

                                    <div class="post-content">

                                        <h4 class="post-title">Willum Ghos <span class="left-date">October 01, 2022</span></h4>
                                        <p class="posts-reply"> Dramatically reinvent adaptive bandwidth vis reliable infrastructures to the progre distributed interfaces have a nice day with <span class="rights-reply"> <i class="bi bi-reply-fill"></i>Reply</span></p>

                                    </div>
                                </div>
                            </div>

                            <!-- contact form box -->
                            <div class="contact-form-box3">
                                <h3 class="blog-comment-title"> Add New Comment </h3>
                                <form action="https://formspree.io/f/myyleorq" method="POST" id="dreamit-form">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <h6 class="form-title"> Name*</h6>
                                            <div class="form-box">
                                                <input type="text" placeholder="Your Name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6"> 
                                            <h6 class="form-title"> Your E-Mail*</h6>
                                            <div class="form-box">
                                                <input type="text" placeholder="Enter E-Mail">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-box">
                                                <h6 class="form-title"> Comment*</h6>
                                                <textarea name="massage" id="massage" cols="30" rows="10" placeholder="Write Comment"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="contact-form style-two">
                                                <button type="submit">  Submit Now </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div id="status"></div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 pl-0 pr-0">
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
                    <h4 class="sidebar-title upp">Popular Post</h4>
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
                {{-- <div class="widget-sidebar-quick-contact">
                    <div class="widget-categories-content text-center">
                        <div class="logo-thumb">
                            <a href="index.html"> <img src="assets/images/logo.png" alt=""> </a>
                        </div>
                        <h3 class="widget-title2" >NEED HELP?</h3>
                        <h5 class="sidebar-title">Call Us</h5>
                        <h5 class="sidebar-title2">+980 (3210) 178</h5>
                        <div class="widget-button">
                            <a href="contact.html"> Contact Us </a>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="widget-sidebar-box upp">
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
                </div> --}}
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
