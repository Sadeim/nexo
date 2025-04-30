<?php

namespace App\Http\Requests\Slider;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class CreateSliderRequest extends BaseRequest
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
            'title' => 'nullable',
            'subtitle' => 'nullable',
            'description' => 'nullable',
            'image' => 'nullable',
            'button_text' => 'nullable',
            'button_link' => 'nullable',
            'order' => 'nullable',
        ];
    }
}