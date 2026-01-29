<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attribute\CreateAttributeRequest;
use App\Http\Requests\Attribute\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class AttributeController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:view_attributes|add_attributes', ['only' => ['index', 'store']]);
    //     $this->middleware('permission:add_attributes', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:edit_attributes', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:delete_attributes', ['only' => ['destroy']]);
    // }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.attributes.index');
    }


    public function datatable(Request $request)
    {
        $items = Attribute::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttributeRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->generateAttributeSlug($request->name);

        try {
            $attribute = Attribute::create($data);
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        return view('admin.attributes.show', ['attribute' => Attribute::findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['attribute'] = Attribute::findOrFail($id);
        return view('admin.attributes.create', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $attribute = Attribute::findOrFail($id);
            $attribute->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function activate($id)
    {
        $item = Attribute::findOrFail($id);
        if (empty($item)) {
            return $this->response_api(404, __('admin.form.not_existed'), '');
        }
        $item->status = 1 - $item->status;
        $item->save();
        return $this->response_api(200,  __('admin.form.status_changed_successfully'), '');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Attribute::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }

    // return json file
    public function getAttributeValues($id)
    {
        $attribute = Attribute::find($id);
        $data['attribute_values'] = $attribute->attributeValues;
        return $this->response_api(200 , __('admin.form.success'), $data);
    }

    function generateAttributeSlug(?string $name, ?string $alternative = null): string
    {
        if (empty($name)) {
            $name = $alternative;
        }

        if (empty($name)) {
            throw new \InvalidArgumentException("A valid name was not provided for code generation.");
        }

        $slug = Str::slug($name);

        if (empty($slug)) {
            $slug = substr(md5($name), 0, 8);
        }

        $originalSlug = $slug;
        $counter = 1;
        while (Attribute::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
