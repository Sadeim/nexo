<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;

class StaticPageController extends Controller
{
    public function show($slug)
    {
        $page = StaticPage::where('slug', $slug)->firstOrFail();
        return view('frontend.static_pages.show', compact('page'));
    }
}
