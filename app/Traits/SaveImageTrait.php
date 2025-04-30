<?php

namespace App\Traits;

trait SaveImageTrait
{
    protected function uploadImage($file, $path = '')
    {
        $file_exe = $file->getClientOriginalExtension();
        $new_name = uniqid() . '.' . $file_exe;
        $directory = 'uploads' . '/' . 'store' . '/' . $path;
        $destienation = public_path($directory);
        $file->move($destienation, $new_name);
        return '/' . $directory . '/' . $new_name;
    }
}
