<?php

namespace App\Http\Requests\Service;

use App\Http\Requests\BaseRequest;

class CreateServiceRequest extends BaseRequest
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
            'icon'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'is_featured'   => ['nullable', 'in:0,1'],
        ];
    }
}
