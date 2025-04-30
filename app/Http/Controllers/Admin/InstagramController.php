<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instagram\CreateInstagramRequest;
use App\Models\Instagram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class InstagramController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_instagrams|add_instagrams', ['only' => ['index','store']]);
        $this->middleware('permission:add_instagrams', ['only' => ['create','store']]);
        $this->middleware('permission:edit_instagrams', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_instagrams', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.instagrams.index');
    }

    public function datatable(Request $request) 
    {
        $items = Instagram::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.instagrams.create');
    }

    public function store(CreateInstagramRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only('link');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'instagrams');
            }

            Instagram::create($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $instagram = Instagram::findOrFail($id);
        return view('admin.instagrams.create', compact('instagram'));
    }

    public function update(CreateInstagramRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $instagram = Instagram::findOrFail($id);
            $data = $request->only('link');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'instagrams');
            }

            $instagram->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Instagram::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
