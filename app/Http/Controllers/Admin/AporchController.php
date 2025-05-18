<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aporch;
use App\Traits\SaveImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AporchController extends Controller
{
    use SaveImageTrait;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.aporch.index');
    }

    public function datatable(Request $request) 
    {
        $items = Aporch::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.aporch.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            DB::beginTransaction();
            $data=$request->all();
            $data = $request->except('image1','image2');
            $imageFields = [
                'image1', 'image2', 
            ];
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $this->uploadImage($request->file($field), 'aporch');
                }
            }
            
             Aporch::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch(\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
       
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $aporch=Aporch::findorfail($id);
        return view('admin.aporch.create',compact('aporch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            DB::beginTransaction();
            $data = $request->except('image1','image2');
            $imageFields = [
                'image1', 'image2'
            ];
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $this->uploadImage($request->file($field), 'aporch');
                }
            }
            $aporch = Aporch::findOrFail($id);
            $aporch->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch(\Exception $e) {
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
        Aporch::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
