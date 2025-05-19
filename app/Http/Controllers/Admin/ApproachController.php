<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Approach\CreateApproachRequest;
use App\Models\Approach;
use App\Traits\SaveImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApproachController extends Controller
{
    use SaveImageTrait;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.approaches.index');
    }

    public function datatable(Request $request) 
    {
        $items = Approach::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.approaches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateApproachRequest $request)
    {
        try {
            DB::beginTransaction();
           
            $data = $request->except([
                'image_1', 'image_2', 'mission_points', 'vision_points', 'value_points'
            ]);

            if ($request->hasFile('image_1')) {
                $data['image_1'] = $this->uploadImage($request->file('image_1'), 'approach');
            }
            if ($request->hasFile('image_2')) {
                $data['image_2'] = $this->uploadImage($request->file('image_2'), 'approach');
            }

            $data['mission_points'] = array_values(array_filter($request->input('mission_points', [])));
            $data['vision_points']  = array_values(array_filter($request->input('vision_points', [])));
            $data['value_points']   = array_values(array_filter($request->input('value_points', [])));

            Approach::create($data);
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
        $approach=Approach::findorfail($id);
        return view('admin.approaches.create',compact('approach'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  Approach $approach)
    {
        //
        try {
            DB::beginTransaction();

            $data = $request->except([
                'image_1', 'image_2',
                'mission_points', 'vision_points', 'value_points'
            ]);

            // 3. تعامل مع رفع الصورتين
            if ($request->hasFile('image_1')) {
                // احذف القديمة إذا وجدت
                if ($approach->image_1) {
                    Storage::disk('public')->delete($approach->image_1);
                }
                $data['image_1'] = $this->uploadImage($request->file('image_1'), 'approach');
            }
            if ($request->hasFile('image_2')) {
                if ($approach->image_2) {
                    Storage::disk('public')->delete($approach->image_2);
                }
                $data['image_2'] = $this->uploadImage($request->file('image_2'), 'approach');
            }

            $data['mission_points'] = array_values(array_filter($request->input('mission_points', [])));
            $data['vision_points']  = array_values(array_filter($request->input('vision_points', [])));
            $data['value_points']   = array_values(array_filter($request->input('value_points', [])));

            $approach->update($data);
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
        Approach::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
