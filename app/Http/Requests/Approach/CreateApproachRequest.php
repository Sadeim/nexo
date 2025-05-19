<?php

namespace App\Http\Requests\Approach;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class CreateApproachRequest extends BaseRequest
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
            'title'               => 'required|string|max:255',
            'subtitle'            => 'nullable|string|max:255',
            'image_1'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_2'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'mission_description' => 'nullable|string',
            'mission_points.*'    => 'nullable|string',
            'vision_description'  => 'nullable|string',
            'vision_points.*'     => 'nullable|string',
            'value_description'   => 'nullable|string',
            'value_points.*'      => 'nullable|string',
        ];
    }
}
