<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{

    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_products|add_products', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_products', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_products', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_products', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.products.index');
    }

    public function datatable(Request $request)
    {
        $items = Product::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest  $request)
    {
        //
        $data = $request->only('name', 'price', 'description', 'image');

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'product_images');
            }

            Product::create($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollBack();
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
        $data['product'] = Product::findOrFail($id);
        return view('admin.products.create', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest  $request, string $id)
    {
        //
        $data = $request->only('name', 'price', 'description', 'image');

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'product_images');
            }

            $product = Product::findOrFail($id);
            $product->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        Product::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
