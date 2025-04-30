<?php

namespace App\Http\Requests\Testimonial;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class CreateTestimonialRequest extends BaseRequest
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
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'message' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'rating' => 'required|integer|between:1,5',
        ];
    }
}