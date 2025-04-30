<?php

namespace App\Traits;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait HasImages
{
    /**
     * Stores the product images.
     */
    public function storeProductImages($product, $mediaRepeater)
    {
        // اضافة صور
        foreach ($mediaRepeater as $key => $media) {
            if(isset($media['image'])){
                $path = $this->saveImageWithSizes($media['image'], 'products/' . $product->id, $key);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path['original'], // حفظ الصورة الأصلية فقط كمرجع
                    'pos' => $key,
                    'image_title' => $media['image_title']
                ]);
            }
        }
    }

    /**
     * Updates product images based on the provided media repeater data.
     */
    public function updateProductImages($product, $mediaRepeater)
    {
        foreach ($mediaRepeater as $key => $media) {
            if (isset($media['id'])) {
                // تعديل الصورة الحالية
                $productImage = ProductImage::find($media['id']);
                if ($productImage) {
                    // تحديث الصورة إذا كانت هناك صورة جديدة
                    if (isset($media['image'])) {
                        Storage::delete('public/' . $productImage->path);
                        $path = $this->saveImageWithSizes($media['image'], 'products/' . $product->id, $key);
                        $productImage->path = $path['original'];
                    }
                    $productImage->pos = $key;
                    $productImage->image_title = $media['image_title'];
                    $productImage->save();
                }
            } else {
                // إضافة صورة جديدة
                if (isset($media['image'])) {
                    $path = $this->saveImageWithSizes($media['image'], 'products/' . $product->id, $key);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path['original'],
                        'pos' => $key,
                        'image_title' => $media['image_title']
                    ]);
                }
            }
        }

        // حذف الصور غير الموجودة في المدخلات
        // $productImageIds = collect($mediaRepeater)->pluck('id')->filter();
        // // dd($productImageIds);
        // ProductImage::where('product_id', $product->id)
        //     ->whereNotIn('id', $productImageIds)
        //     ->get()
        //     ->each(function ($productImage) {
        //         Storage::delete(str_replace('_original', "_large", $productImage->path));
        //         Storage::delete(str_replace('_original', "_medium", $productImage->path));
        //         Storage::delete(str_replace('_original', "_thumbnail", $productImage->path));
        //         Storage::delete($productImage->path);
        //         $productImage->delete();
        //     });
    }

    private function saveImageWithSizes($imageFile, $path, $pos)
    {
        $sizes = [
            'large' => [1024, 768],
            'medium' => [512, 384],
            'thumbnail' => [150, 150],
        ];

        $paths = [];
        foreach ($sizes as $sizeName => $dimensions) {
            $img = Image::make($imageFile)
                ->resize($dimensions[0], $dimensions[1], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            $fileName = "{$path}_{$sizeName}" . $pos . "." . $imageFile->getClientOriginalExtension();
            Storage::put($fileName, (string) $img->encode());
            $paths[$sizeName] = $fileName;
        }

        // حفظ الصورة الأصلية
        $originalPath = "{$path}_original" . $pos . "." .  $imageFile->getClientOriginalExtension();
        Storage::put($originalPath, file_get_contents($imageFile));
        $paths['original'] = $originalPath;

        return $paths;
    }
}
