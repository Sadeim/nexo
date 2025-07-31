<?php

namespace App\Http\Requests\Attribute;

use App\Http\Requests\BaseRequest;
use Illuminate\Http\Request;

class UpdateAttributeRequest extends BaseRequest
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
            'name'           => ['required', 'max:255'],
            'description'    => ['nullable'],
        ];
    }
}
