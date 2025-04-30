<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\CreateBlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    use SaveImageTrait;
    
    public function __construct()
    {
        $this->middleware('permission:view_blogs|add_blogs', ['only' => ['index','store']]);
        $this->middleware('permission:add_blogs', ['only' => ['create','store']]);
        $this->middleware('permission:edit_blogs', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_blogs', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        return view('admin.blogs.index');
    }
    
    public function datatable(Request $request) 
    {
        $items = Blog::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.blogs.create');
    }
    
    public function store(CreateBlogRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['title', 'category', 'author', 'published_at', 'content', 'status']);
            $data['slug'] = Str::slug($data['title']);
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'blogs');
            }
            Blog::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch(\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blogs.create', compact('blog'));
    }
    
    public function update(CreateBlogRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['title', 'category', 'author', 'published_at', 'content', 'status']);
            $data['slug'] = Str::slug($data['title']);
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'blogs');
            }
            $blog = Blog::findOrFail($id);
            $blog->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch(\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    
    public function destroy($id)
    {
        Blog::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
