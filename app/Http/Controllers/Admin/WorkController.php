<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Work\CreateWorkRequest;
use App\Models\Section;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Traits\SaveImageTrait;

class WorkController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_works|add_works', ['only' => ['index','store']]);
        $this->middleware('permission:add_works', ['only' => ['create','store']]);
        $this->middleware('permission:edit_works', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_works', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['section'] = Section::where('key', 'works_section')->first();
        return view('admin.works.index', $data);
    }

    public function datatable(Request $request) 
    {
        $items = Work::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.works.create');
    }

    public function store(CreateWorkRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['title', 'category', 'description', 'status','is_featured']);
            $data['slug'] = Str::slug($data['title'] . '-' . rand(1000,9999));
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'works');
            }
            Work::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $work = Work::findOrFail($id);
        return view('admin.works.create', compact('work'));
    }

    public function update(CreateWorkRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['title', 'category', 'description', 'status','is_featured']);
            $data['slug'] = Str::slug($data['title'] . '-' . rand(1000,9999));
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'works');
            }
            $work = Work::findOrFail($id);
            $work->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Work::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
