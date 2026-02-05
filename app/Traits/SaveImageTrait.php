<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

trait SaveImageTrait
{
    // protected function uploadImage($file, $path = '')
    // {
    //     $file_exe = $file->getClientOriginalExtension();
    //     $new_name = uniqid() . '.' . $file_exe;
    //     $directory = 'uploads' . '/' . 'store' . '/' . $path;
    //     $destienation = public_path($directory);
    //     $file->move($destienation, $new_name);
    //     return '/' . $directory . '/' . $new_name;
    // }

    // protected function uploadImage($file, $path = '')
    // {
    //     $file_exe = $file->getClientOriginalExtension();
    //     $new_name = uniqid() . '.' . $file_exe;
    //     $directory = 'uploads/' . $path;
    //     $destination = public_path($directory);

    //     if (!file_exists($destination)) {
    //         mkdir($destination, 0755, true);
    //     }

    //     // Resize
    //     $image = Image::make($file)->resize(1200, null, function ($constraint) {
    //         $constraint->aspectRatio();
    //         $constraint->upsize(); // لا تعمل upscale لو الصورة أصغر
    //     });

    //     $image->save($destination . '/' . $new_name, 75); // 75% جودة

    //     return '/' . $directory . '/' . $new_name;
    // }

    // protected function uploadImage($file, $path = '')
    // {
    //     $file_exe = $file->getClientOriginalExtension();
    //     $new_name = uniqid() . '.' . $file_exe;
    //     $directory = 'uploads' . '/' . 'store' . '/' . $path;
    //     $destination = public_path($directory);

    //     // إنشاء المجلد تلقائياً إذا لم يكن موجوداً
    //     if (!file_exists($destination)) {
    //         mkdir($destination, 0777, true);
    //         chmod($destination, 0777); // تأكيد الصلاحيات
    //     }

    //     $file->move($destination, $new_name);
    //     return '/' . $directory . '/' . $new_name;
    // }

    // protected function uploadImage($file, $path = '')
    // {
    //     // تسجيل حجم الملف الأصلي
    //     Log::info('Original file size: ' . $file->getSize() . ' bytes');

    //     $file_exe = $file->getClientOriginalExtension();
    //     $new_name = uniqid() . '.' . $file_exe;
    //     $directory = 'uploads' . '/' . 'store' . '/' . $path;
    //     $destination = public_path($directory);

    //     // إنشاء المجلد تلقائياً إذا لم يكن موجوداً
    //     if (!file_exists($destination)) {
    //         mkdir($destination, 0755, true);
    //     }

    //     // قراءة الصورة بدون ضغط
    //     $image = Image::make($file->getRealPath());

    //     // حفظ الصورة بجودة 100% مباشرة
    //     $fullPath = $destination . '/' . $new_name;
    //     $image->save($fullPath, 100);

    //     // تسجيل حجم الملف بعد الرفع
    //     if (file_exists($fullPath)) {
    //         $uploadedSize = filesize($fullPath);
    //         Log::info('Uploaded file size: ' . $uploadedSize . ' bytes');

    //         // حساب نسبة الضغط
    //         $compressionRatio = round((1 - ($uploadedSize / $file->getSize())) * 100, 2);
    //         Log::info('Compression ratio: ' . $compressionRatio . '%');
    //     }

    //     return '/' . $directory . '/' . $new_name;
    // }
    protected function uploadImage($file, $path = '')
    {
        // تسجيل حجم الملف الأصلي
        Log::info('Original file size: ' . $file->getSize() . ' bytes');

        $file_exe = $file->getClientOriginalExtension();
        $new_name = uniqid() . '.' . $file_exe;
        $directory = 'uploads' . '/' . 'store' . '/' . $path;
        $destination = public_path($directory);

        // إنشاء المجلد تلقائياً إذا لم يكن موجوداً
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $fullPath = $destination . '/' . $new_name;

        // نسخ الملف مباشرة بدون أي معالجة (يحافظ على الأبعاد والجودة 100%)
        copy($file->getRealPath(), $fullPath);

        // تسجيل حجم الملف بعد الرفع
        if (file_exists($fullPath)) {
            $uploadedSize = filesize($fullPath);
            Log::info('Uploaded file size: ' . $uploadedSize . ' bytes');

            // حساب نسبة الضغط
            $compressionRatio = round((1 - ($uploadedSize / $file->getSize())) * 100, 2);
            Log::info('Compression ratio: ' . $compressionRatio . '%');

            // تسجيل أبعاد الصورة
            if (function_exists('getimagesize')) {
                $imageInfo = getimagesize($fullPath);
                if ($imageInfo) {
                    Log::info('Image dimensions: ' . $imageInfo[0] . ' x ' . $imageInfo[1] . ' pixels');
                }
            }
        }

        return '/' . $directory . '/' . $new_name;
    }
}
