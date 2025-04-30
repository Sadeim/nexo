<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_categories|add_categories', ['only' => ['index','store']]);
        $this->middleware('permission:add_categories', ['only' => ['create','store']]);
        $this->middleware('permission:edit_categories', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_categories', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.categories.index');
    }

    public function datatable(Request $request) 
    {
        $items = Category::query()->orderBy('id', 'DESC')->search($request);
        return $this->filterDataTable($items, $request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryRequest $request)
    {            
        $dataR = $request->only('image', 'status', 'category_id', 'name', 'description');

        try {
            DB::beginTransaction();
                if (request()->has('image')) {
                    $dataR['image'] = $this->uploadImage($request->image, 'course_images');
                }

                $slug_exist = Category::where('name', $request->name)->first();
                if(!isset($slug_exist)){
                    $dataR['slug'] = $this->makeEnglishSlug($request->name);
                }else{
                    $dataR['slug'] = $request->name . '-' . rand(100000,999999);
                }

                Category::create($dataR);
            DB::commit();
    
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['category'] = Category::findOrFail($id);
        return view('admin.categories.create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $dataR = $request->only('image', 'status', 'category_id', 'name', 'description');

        try {
            DB::beginTransaction();
                if (request()->has('image')) {
                    $dataR['image'] = $this->uploadImage($request->image, 'course_images');
                }
                
                $slug_exist = Category::where('name', $request->name)->first();
                if(!isset($slug_exist)){
                    $dataR['slug'] = $this->makeEnglishSlug($request->name);
                }else{
                    $dataR['slug'] = $request->name . '-' . rand(100000,999999);
                }

                $category = Category::findOrFail($id);
                $category->update($dataR);
            DB::commit();

            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function activate($id)
    {
        $item = Category::findOrFail($id);
        if (empty($item)) {
            return $this->response_api(400, __('admin.form.not_existed'), '');
        }
        $item->status = 1 - $item->status;
        $item->save();
        return $this->response_api(200,  __('admin.form.status_changed_successfully'), '');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }


    public function bluckDestroy(Request $request) 
    {
        $ids = $request->id;
        foreach ($ids as $row) {
            $item = Category::find($row);
            if(!$item) {
                return $this->response_api(400 ,  __('admin.form.not_existed') , '');
            }
            $item->delete();
        }
        return $this->response_api(200 ,  __('admin.form.deleted_successfully') , '');
    }
}
