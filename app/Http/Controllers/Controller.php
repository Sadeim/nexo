<?php

namespace App\Http\Controllers;

use App\Http\Mail\SendClientActivationCode;
use App\Http\Mail\SendResetPasswordLink;
use App\Jobs\SendSms;
use App\Models\Product;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $guard_name;

    // public function __construct()
    // {
        // dd(Auth::guard()->name);
        // $this->middleware(function ($request, $next) {
        //     $this->guard_name = Auth::guard()->name;
        //     return $next($request);
        // });
    // }

    public function response_api($status, $message, $items = null)
    {
        $response = ['status' => $status, 'message' => $message];
        if ($status && isset($items)) {
            $response['item'] = $items;
        } else {
            $response['errors_object'] = $items;
        }
        return response()->json($response, $status);
    }


    public function filterDataTable($items, $request,$take = null,$resource = null)
    {
        if ($items->count() <= 0) {
            $data['recordsTotal'] = 0;
            $data['recordsFiltered'] = 0;
            $data['data'] = [];
            return $data;
        }

        if (!$resource) {
            $resource = $items->first()->resource;
        }

        if (isset($take)) {
            $items = $items->take($take)->get();
            $data = $resource->collection($items);
            return $data;
        }
        $per_page = isset($request->length) ? $request->length : 10;
        $page = isset($request->start) ? $request->start : 1;
        if ($per_page == -1 || $per_page == null) {
            $per_page = 10;
        }
        $itemsCount = $items->count();
        $items = $items->take($per_page)->skip($page)->get();
        $data['recordsTotal'] = $itemsCount;
        $data['recordsFiltered'] = $itemsCount;
        $data['data'] = $resource::collection($items);
        return $data;
    }

    public function sendActivationCode($name, $email, $title, $body, $activation_code, $type='')
    {
        $data['activation_code'] = $activation_code;
        $data['title'] = $title;
        $data['body'] = $body;
        $data['name'] = $name;
        $data['email'] = $email;
        $data['lang'] = $this->lang();

        try {
            Mail::to($email)->send(new SendClientActivationCode($data));
            return true;
        } catch (Exception $exception) {
            Log::error('admin : ' . $data['email'] . '-' . $exception->getMessage());
            return false;
        }
    }

    public function sendResetPasswordLink($name, $email, $title, $body, $token)
    {
        $data['link'] = route('admin.showResetPasswordForm',$token);
        $data['title'] = $title;
        $data['body'] = $body;
        $data['name'] = $name;
        $data['email'] = $email;
        $data['lang'] = $this->lang();

        try {
            Mail::to($email)->send(new SendResetPasswordLink($data));
            return true;
        }catch (Exception $exception){
            Log::error('admin : ' . $data['email'] . '-' . $exception->getMessage());
            return false;
        }
    }

    function exMessage($e)
    {
        if (env('APP_ENV')  == 'production') {
            return __('admin.form.some_errors');
        } else {
            return $e->getMessage();
        }
    }

    // public function sendResponse($result, $message)
    // {
    //     $main_response = [
    //         'code' => 200,
    //         'success' => true,
    //         'message' => $message,
    //     ];

    //     if($result !== null){
    //         $data = [
    //             'data' => $result
    //         ];
    //     }else{
    //         $data = [];
    //     }

    //     $response = array_merge($main_response, $data);

    //     return response()->json($response, 200);
    // }

    public function sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'code' => 400,
            'success' => false,
            'message' => $error
        ];
        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);

    }

    protected function sendOTP($phone)
    {
        $otp = rand(1000, 9999);
        // $otp= '0000';

        $this->sendSms('Your OTP is ' . $otp, $phone);
        return $otp;
    }

    public function sendSms($message, $number)
    {
        SendSms::dispatchSync($message,$number);
    }

    // function uploadFile($img, $type = null, $owner_id = null, $owner_type = null)
    // {
    //     if (!$img->getClientOriginalExtension()) {
    //         return null;
    //     }
    //     $extension = $img->getClientOriginalExtension();

    //     $filename = 'file_' . time() . mt_rand();
    //     $repo = new FileLogic();

    //     $allowed_filename = $repo->createUniqueFilename($filename, $extension);

    //     $uploadSuccess = $repo->original($img, $allowed_filename);

    //     $originalName = str_replace('.' . $extension, '', $img->getClientOriginalName());

    //     $file = new File();
    //     $file->display_name = $originalName . '.' . $extension;
    //     $file->file_name = $allowed_filename;
    //     $file->mime_type = $extension;
    //     $file->size = $img->getSize();
    //     $file->owner_id = $owner_id;
    //     $file->owner_type = $owner_type;
    //     $file->save();
    //     $url = $file->file_name;

    //     return $url;
    // }

    function makeEnglishSlug($str) 
    { 
        $str = strtolower($str); 

        $str = preg_replace('/[^a-z0-9]+/', '-', $str); 
        
        $str = trim($str, '-'); 
        
        return $str; 
    } 

    function makeArabicSlug($str) 
    { 
        $str = strtolower($str); 

        $str = str_replace(' ', '-', $str); 
        
        $str = trim($str, '-'); 
        
        return $str; 
    } 

    function convertArNumberToEnNumber($number)
    {
        $arabic_eastern = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
        $arabic_western = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        return str_replace($arabic_eastern, $arabic_western, $number);
    }

    protected function generateSlug($nameAr, $nameEn)
    {
        $slug = $nameAr ? $this->makeArabicSlug($nameAr) : $this->makeEnglishSlug($nameEn);
        $slugExist = Product::whereTranslation('name', $nameAr)->exists();

        if ($slugExist) {
            $slug .= '-' . rand(100000, 999999);
        }

        return $slug;
    }

    private function saveImageWithSizes($imageFile, $path)
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

            $fileName = "{$path}_{$sizeName}." . $imageFile->getClientOriginalExtension();
            Storage::put($fileName, (string) $img->encode());
            $paths[$sizeName] = $fileName;
        }

        // حفظ الصورة الأصلية
        $originalPath = "{$path}_original." . $imageFile->getClientOriginalExtension();
        Storage::put($originalPath, file_get_contents($imageFile));
        $paths['original'] = $originalPath;

        return $paths;
    }
}

