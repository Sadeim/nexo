<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\CreateFeatureRequest;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class FeatureController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_features|add_features', ['only' => ['index','store']]);
        $this->middleware('permission:add_features', ['only' => ['create','store']]);
        $this->middleware('permission:edit_features', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_features', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.features.index');
    }

    public function datatable(Request $request) 
    {
        $items = Feature::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.features.create');
    }

    public function store(CreateFeatureRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'features');
            }

            Feature::create($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $feature = Feature::findOrFail($id);
        return view('admin.features.create', compact('feature'));
    }

    public function update(CreateFeatureRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $feature = Feature::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'features');
            }

            $feature->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Feature::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
