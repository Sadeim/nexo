<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
 
class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->take(3)->get(); // للواجهة الرئيسية
        return view('frontend.home', compact('blogs'));
    }
}
