<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateSettingRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_logo'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'email'                 => ['nullable', 'max:255'],
            'phone'                 => ['nullable'],
            'whatsapp'              => ['nullable'],
            'linkedin'              => ['nullable', 'max:255'],
            'facebook'              => ['nullable', 'max:255'],
            // 'telegram'              => ['required', 'max:255'],
            'instagram'             => ['nullable', 'max:255'],
            'twitter'               => ['nullable', 'max:255'],
            'address'               => ['nullable', 'max:255'],
            'web_address'           => ['nullable', 'max:255'],
            'map_embed'             => ['nullable'],
            'site_description'      => ['nullable', 'max:255'],
        ];
    }
}
