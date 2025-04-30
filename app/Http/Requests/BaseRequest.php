<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $response = ['status' => false, 'message' => $validator->errors()->first()];
        throw new HttpResponseException(response()->json($response));
    }
}
