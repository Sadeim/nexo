<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Section;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $data['blogs'] = Blog::latest()->paginate(6);
        $data['about'] = About::first();
        $data['section'] = Section::where('key', 'blog_page')->first();

        // $data['popular_posts'] = Blog::limit(3)->get();
        // $data['categories'] = Category::active()->get();
        return view('frontend.blog.index', $data);
    }

    public function show()
    {
        $data['blog'] = Blog::where('slug', request()->slug)->firstOrFail();
        $data['section'] = Section::where('key', 'blog_page')->first();
        // $data['popular_posts'] = Blog::limit(3)->get();
        // $data['categories'] = Category::active()->get();
        return view('frontend.blog.show', $data);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $results = Blog::where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->take(10)
                    ->get();
    
        return view('frontend.blog.partials.search_results', compact('results'))->render();
    }
}
