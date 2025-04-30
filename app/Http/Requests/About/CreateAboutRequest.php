<?php

namespace App\Http\Requests\About;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class CreateAboutRequest extends BaseRequest
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
            'company_name'       => 'required|string|max:255',
            'sub_title'          => 'nullable|string|max:255',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'image1'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image2'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image3'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'circle_text_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'logo_icon'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'author_name'        => 'nullable|string|max:255',
            'author_position'    => 'nullable|string|max:255',
            'author_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'signature_image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'check1'             => 'nullable|string|max:255',
            'check2'             => 'nullable|string|max:255',
            'check3'             => 'nullable|string|max:255',
        ];
    }
}