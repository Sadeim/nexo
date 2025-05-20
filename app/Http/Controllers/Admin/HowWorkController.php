<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\How;
use App\Models\Work;
use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HowWorkController extends Controller
{
    use SaveImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('permission:view_how|add_how', ['only' => ['index','store']]);
        $this->middleware('permission:add_how', ['only' => ['create','store']]);
        $this->middleware('permission:edit_how', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_how', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('admin.how.index');
    }
    public function datatable(Request $request)
    {
        $items = How::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.how.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            DB::beginTransaction();
            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'how');
            }
            How::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $how=How::findorfail($id);
        return view('admin.how.create', compact('how'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            DB::beginTransaction();
            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'how');
            }
            $how=How::findorfail($id);
            $how->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        How::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
