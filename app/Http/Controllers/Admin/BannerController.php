<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\CreateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class BannerController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_banners|add_banners', ['only' => ['index','store']]);
        $this->middleware('permission:add_banners', ['only' => ['create','store']]);
        $this->middleware('permission:edit_banners', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_banners', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.banners.index');
    }

    public function datatable(Request $request) 
    {
        $items = Banner::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(CreateBannerRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only('title', 'sub_title', 'description', 'button_link', 'button_text');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'banners');
            }

            $banner = Banner::first();
            if ($banner) {
                $banner->update($data);
            } else {
                Banner::create($data);
            }

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.create', compact('banner'));
    }

    public function update(CreateBannerRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $banner = Banner::findOrFail($id);
            $data = $request->only('title', 'sub_title', 'description', 'button_link', 'button_text');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'banners');
            }

            $banner->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Banner::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }

    public function activate($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = 1 - $banner->status;
        $banner->save();
        return $this->response_api(200, __('admin.form.status_changed_successfully'), '');
    }
}
