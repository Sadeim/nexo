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

            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'image1'             => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'image2'             => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'check1'             => 'nullable|string|max:255',
            'check2'             => 'nullable|string|max:255',
            'check3'             => 'nullable|string|max:255',
        ];
    }
}
