<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\Variant;
// use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{

    // use SaveImageTrait;

    public function __construct()
    {
        // $this->middleware('permission:view_products|add_products', ['only' => ['index', 'store']]);
        // $this->middleware('permission:add_products', ['only' => ['create', 'store']]);
        // $this->middleware('permission:edit_products', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:delete_products', ['only' => ['destroy']]);
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
        $data['attributes'] = Attribute::active()->get();
        return view('admin.products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest  $request)
    {
        //
        $data = $request->only('name', 'price', 'description', 'image');

        $product = Product::create($data);

        try {
            DB::beginTransaction();

            // if ($request->hasFile('image')) {
            //     $data['image'] = $this->uploadImage($request->image, 'product_images');
            // }
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                 
                      $variant = $product->variants()->create([
                        'price' => $variantData['price'],
                         'sku' => $variantData['sku'] ?? null,
                    ]);

                  
                    if (!empty($variantData['attribute_values'])) {
                        $attributesToSync = [];
                        foreach ($variantData['attribute_values'] as $value_id) {
                            if ($value_id !== null) {
                                $attributesToSync[] = $value_id;
                            }
                        }
                        $variant->attributeValues()->sync($attributesToSync);
                    }

                    $variant->inventory()->create([
                        'quantity' => $variantData['quantity'] ?? 0,
                    ]);
                }
            } else {
                $product->price = $request->price;
                $product->save();
            }

             if (!empty($request->media_repeater)) {
                $product->storeProductImages($product, $request->media_repeater);
            }

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
        $data['attributes'] = Attribute::active()->get();
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

            // if ($request->hasFile('image')) {
            //     $data['image'] = $this->uploadImage($request->image, 'product_images');
            // }

            $product = Product::findOrFail($id);
            $product->update($data);

                if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['id'])) {
                        $variant = Variant::find($variantData['id']);

                        if ($variant) {
                            $variant->update([
                                'price' => $variantData['price'],
                                'sku'   => $variantData['sku'] ?? null,
                            ]);

                            // تحديث الكمية عبر علاقة polymorphic
                            $variant->inventory()->updateOrCreate(
                                [], // لا شرط، لأنه morphOne مرتبط بـ variant
                                ['quantity' => $variantData['quantity'] ?? 0]
                            );
                        }
                    } else {
                        $variant = $product->variants()->create([
                            'price' => $variantData['price'],
                            'sku'   => $variantData['sku'] ?? null,
                        ]);

                        $variant->inventory()->create([
                            'quantity' => $variantData['quantity'] ?? 0,
                        ]);
                    }

                    if (!empty($variantData['attribute_values']) && is_array($variantData['attribute_values'])) {
                        $attributesToSync = array_filter(array_values($variantData['attribute_values']));
                        $variant->attributeValues()->sync($attributesToSync);
                    }
                }
            } else {
                $product->price = $request->price;
                $product->save();

                $product->inventory()->updateOrCreate(
                    [], // لا شرط، لأنه morphOne مرتبط بـ product
                    ['quantity' => $request->quantity ?? 0]
                );
            }
              if (!empty($request->media_repeater)) {
                $product->updateProductImages($product, $request->media_repeater);
            }

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
