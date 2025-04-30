<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Slider\CreateSliderRequest;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class SliderController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_sliders|add_sliders', ['only' => ['index','store']]);
        $this->middleware('permission:add_sliders', ['only' => ['create','store']]);
        $this->middleware('permission:edit_sliders', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_sliders', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.sliders.index');
    }

    public function datatable(Request $request) 
    {
        $items = Slider::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(CreateSliderRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'sliders');
            }

            Slider::create($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('admin.sliders.create', compact('slider'));
    }

    public function update(CreateSliderRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $slider = Slider::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'sliders');
            }

            $slider->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Slider::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
