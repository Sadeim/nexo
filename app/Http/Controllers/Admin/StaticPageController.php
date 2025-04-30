<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaticPage\UpdateStaticPageRequest;
use App\Http\Requests\StaticPage\CreateStaticPageRequest;
use App\Models\Category;
use App\Models\StaticPage;
use App\Models\Product;
use App\Models\ProductStaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaticPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_static_pages|add_static_pages', ['only' => ['index','store']]);
        $this->middleware('permission:add_static_pages', ['only' => ['create','store']]);
        $this->middleware('permission:edit_static_pages', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_static_pages', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.static_pages.index');
    }

    public function datatable(Request $request) 
    {
        $items = StaticPage::query()->orderBy('id', 'DESC'); 
        return $this->filterDataTable($items, $request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.static_pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStaticPageRequest $request)
    {           
        $dataR = $request->only('slug', 'status', 'title', 'content');
    
        try {
            StaticPage::create($dataR);
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(404, $this->exMessage($e));
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
        $data['static_page'] = StaticPage::findOrFail($id);
        return view('admin.static_pages.create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStaticPageRequest $request, $id)
    {
        $dataR = $request->only('slug', 'status', 'title', 'content');

        try {    
            DB::beginTransaction();
                $static_page = StaticPage::findOrFail($id);
                $static_page->update($dataR);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(404, $this->exMessage($e));
        }
    }

    public function activate($id)
    {
        $item = StaticPage::findOrFail($id);
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
        StaticPage::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
