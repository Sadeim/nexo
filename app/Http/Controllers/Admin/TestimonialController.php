<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Testimonial\CreateTestimonialRequest;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Traits\SaveImageTrait;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_testimonials|add_testimonials', ['only' => ['index','store']]);
        $this->middleware('permission:add_testimonials', ['only' => ['create','store']]);
        $this->middleware('permission:edit_testimonials', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_testimonials', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['section'] = Section::where('key', 'testimonials_section')->first();
        return view('admin.testimonials.index', $data);
    }

    public function datatable(Request $request) 
    {
        $items = Testimonial::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(CreateTestimonialRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only(['name', 'position', 'message', 'rating']);
            if ($request->hasFile('photo')) {
                $data['photo'] = $this->uploadImage($request->photo, 'testimonials');
            }

            Testimonial::create($data);
            DB::commit();

            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return view('admin.testimonials.create', compact('testimonial'));
    }

    public function update(CreateTestimonialRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $data = $request->only(['name', 'position', 'message', 'rating']);
            if ($request->hasFile('photo')) {
                $data['photo'] = $this->uploadImage($request->photo, 'testimonials');
            }

            $testimonial = Testimonial::findOrFail($id);
            $testimonial->update($data);

            DB::commit();

            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Testimonial::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}