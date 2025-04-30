<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class CreateCategoryRequest extends BaseRequest
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
            'image'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'status'            => ['nullable', 'in:0,1'],
            'name'           => ['required', 'max:255'],
            'description'    => ['nullable'],
        ];
    }
}