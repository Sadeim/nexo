<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItem\CreateMenuItemRequest;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class MenuItemController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_menu_items|add_menu_items', ['only' => ['index','store']]);
        $this->middleware('permission:add_menu_items', ['only' => ['create','store']]);
        $this->middleware('permission:edit_menu_items', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_menu_items', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.menu_items.index');
    }

    public function datatable(Request $request) 
    {
        $items = MenuItem::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        $data['categories'] = Category::active()->get();
        return view('admin.menu_items.create', $data);
    }

    public function store(CreateMenuItemRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'menu_items');
            }

            MenuItem::create($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $data['menu_item'] = MenuItem::findOrFail($id);
        $data['categories'] = Category::active()->get();
        return view('admin.menu_items.create', $data);
    }

    public function update(CreateMenuItemRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $menu_item = MenuItem::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'menu_items');
            }

            $menu_item->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        MenuItem::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
