<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class CreateBookingRequest extends BaseRequest
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
            'name' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'persons' => 'nullable',
            'date' => 'nullable',
            'time' => 'nullable',
            'message' => 'nullable',
            'status' => 'nullable',
        ];
    }
}