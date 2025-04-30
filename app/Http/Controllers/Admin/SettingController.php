<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingRequest;
use App\Models\Setting;
use App\Traits\SaveImageTrait;
use Exception;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use SaveImageTrait;

    public function index() 
    {
        $data['settings'] = new Setting();
        return view('admin.settings.index' , $data);
    }

    public function update(UpdateSettingRequest $request) 
    {
        $data = $request->validated();
        try {
            foreach ($data as $key => $value) {
                if (!is_null($value)) {
                    if (request()->has('company_logo') && $key == 'company_logo') {
                        $value = $this->uploadImage($request->company_logo, 'company_logo');
                    }
                    Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                }
            }
            return $this->response_api(200, __('admin.form.added_successfully'), '');

        } catch (Exception $e) {
            // return $this->response_api(400, $e->getMessage());
            return back()->with(['msg_status' => 'error','msg_content' => $e->getMessage()]);
        }

    }
}
