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
            'image1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'tab1_title'    => 'nullable|string|max:255',
            'tab2_title'    => 'nullable|string|max:255',
            'tab1_content'  => 'nullable|string|max:1000',
            'tab2_content'  => 'nullable|string|max:1000',
            'button_text'   => 'nullable|string|max:255',
            'button_link'   => 'nullable|max:255',

            'opening_hours' => 'nullable|array',
            'opening_hours.*.day' => 'required_with:opening_hours|string|max:20',
            'opening_hours.*.from' => 'nullable|date_format:H:i',
            'opening_hours.*.to' => 'nullable|date_format:H:i',
            'opening_hours.*.status' => 'nullable|boolean',
        ];
    }
}
