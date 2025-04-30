<?php

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

function admin()
{
    if (auth()->guard('admin')->check()) {
        return auth()->guard('admin')->user();
    }
}

function locales()
{
    return ['ar', 'en'];
}

function checkNull($key_name)
{
    return request()->has($key_name) && request()->$key_name != null && request()->$key_name != '';
}

function errorResponse($message)
{
    $array = [
        'status' => false,
        'message' => $message,
        'data' => null,
    ];
    return response($array, 200);
}

function containsOnlyNull($input)
{
    return empty(array_filter($input, function ($a) {
        return $a !== null;
    }));
}

function getImageUrl($imagePath, $size = 'original') 
{
    return Storage::url(str_replace('_original', "_$size", $imagePath));
}

function getCartCountFromCookies()
{
    $cart = json_decode(Cookie::get('cart_s', '[]'), true);
    if (is_array($cart)) {
        return count($cart);
    }

    return 0;
}
