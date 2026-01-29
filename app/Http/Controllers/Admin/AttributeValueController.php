<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeValue\CreateAttributeValueRequest;
use App\Http\Requests\AttributeValue\UpdateAttributeValueRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttributeValueController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:view_attribute_values|add_attribute_values', ['only' => ['index', 'store']]);
    //     $this->middleware('permission:add_attribute_values', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:edit_attribute_values', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:delete_attribute_values', ['only' => ['destroy']]);
    // }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.attribute_values.index');
    }

    public function datatable(Request $request)
    {
        $items = AttributeValue::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['attributes'] = Attribute::active()->get();
        return view('admin.attribute_values.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttributeValueRequest $request)
    {
        $data = $request->validated();
        $dataR['slug'] = $this->generateAttributeValueSlug($request->name);

        try {
            $attribute_value = AttributeValue::create($data);
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
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['attribute_value'] = AttributeValue::findOrFail($id);
        $data['attributes'] = Attribute::active()->get();
        return view('admin.attribute_values.create', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeValueRequest $request, $id)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $attribute_value = AttributeValue::findOrFail($id);
            $attribute_value->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function activate($id)
    {
        $item = AttributeValue::findOrFail($id);
        if (empty($item)) {
            return $this->response_api(404, __('admin.form.not_existed'), '');
        }
        $item->status = 1 - $item->status;
        $item->save();
        return $this->response_api(200,  __('admin.form.status_changed_successfully'), '');
    }

    function generateAttributeValueSlug(?string $name, ?string $alternative = null): string
    {
        if (empty($name)) {
            $name = $alternative;
        }

        if (empty($name)) {
            throw new \InvalidArgumentException("Incorrect name.");
        }

        $slug = Str::slug($name);

        if (empty($slug)) {
            $slug = substr(md5($name), 0, 8);
        }

        $originalSlug = $slug;
        $counter = 1;
        while (AttributeValue::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        AttributeValue::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
