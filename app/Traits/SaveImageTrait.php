<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


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

    protected function uploadImage($file, $path = '')
    {
        // تسجيل حجم الملف الأصلي قبل الرفع
        Log::info('Original file size: ' . $file->getSize() . ' bytes');

        $file_exe = $file->getClientOriginalExtension();
        $new_name = uniqid() . '.' . $file_exe;
        $directory = 'store' . '/' . $path;

        // التأكد من وجود المجلد في storage/app/public
        $fullDirectory = storage_path('app/public/' . $directory);
        if (!file_exists($fullDirectory)) {
            mkdir($fullDirectory, 0755, true);
        }

        // قراءة الصورة بدون ضغط
        $image = Image::make($file->getRealPath());

        // حفظ الصورة بجودة 100% (بدون ضغط)
        $image->encode($file_exe, 100);

        // حفظ الصورة
        $fullPath = $directory . '/' . $new_name;
        Storage::disk('public')->put($fullPath, (string) $image);

        // تسجيل حجم الملف بعد الرفع
        $uploadedFilePath = storage_path('app/public/' . $fullPath);
        if (file_exists($uploadedFilePath)) {
            $uploadedSize = filesize($uploadedFilePath);
            Log::info('Uploaded file size: ' . $uploadedSize . ' bytes');

            // حساب نسبة الضغط
            $compressionRatio = round((1 - ($uploadedSize / $file->getSize())) * 100, 2);
            Log::info('Compression ratio: ' . $compressionRatio . '%');
        }

        return '/storage/' . $fullPath;
    }
}
