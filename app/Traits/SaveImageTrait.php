<?php

namespace App\Traits;

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

    protected function uploadImage($file, $path = '')
    {
        $file_exe = $file->getClientOriginalExtension();
        $new_name = uniqid() . '.' . $file_exe;
        $directory = 'uploads' . '/' . 'store' . '/' . $path;
        $destination = public_path($directory);

        // إنشاء المجلد تلقائياً إذا لم يكن موجوداً
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
            chmod($destination, 0777); // تأكيد الصلاحيات
        }

        $file->move($destination, $new_name);
        return '/' . $directory . '/' . $new_name;
    }
}
