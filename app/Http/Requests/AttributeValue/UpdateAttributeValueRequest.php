<?php

namespace App\Http\Requests\AttributeValue;

use App\Http\Requests\BaseRequest;
use Illuminate\Http\Request;

class UpdateAttributeValueRequest extends BaseRequest
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
            'attribute_id'      => ['required', 'exists:attributes,id'],
            'name'           => ['required', 'max:255'],
            'description'    => ['nullable'],
        ];
    }
}
