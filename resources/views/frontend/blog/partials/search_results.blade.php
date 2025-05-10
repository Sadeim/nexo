@if ($results->count())
    <ul class="list-group">
        @foreach($results as $blog)
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
                            <li> <i class="bi bi-chat"></i>{{ strtoupper(\Carbon\Carbon::parse($blog->created_at)->format('d M, Y')) }} </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </ul>
@else
    <p>No results found.</p>
@endif
