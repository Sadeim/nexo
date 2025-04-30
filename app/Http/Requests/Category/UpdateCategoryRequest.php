<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseRequest;
use Illuminate\Http\Request;

class UpdateCategoryRequest extends BaseRequest
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
    public function rules(Request $request)
    {
        return [
            'image'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'status'            => ['nullable', 'in:0,1'],
            'name'           => ['required', 'max:255'],
            'description'    => ['nullable'],
        ];
    }
}
