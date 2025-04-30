<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Traits\Rule\CustomValidationRulesTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseRequest
{
    use CustomValidationRulesTrait;

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
        $check_phone = $this->checkPhone('users', $this->request->get('phone_code'), $this->request->get('phone'), $request->user);

        return [
            'first_name'        => ['required', 'max:255'],
            'last_name'         => ['required', 'max:255'],
            'email'             => ['required',  'unique:users,email,' . $request->user . ',id'],
            'password'          => ['nullable', 'max:255'],
            'phone'             => ['required', 'digits_between:9,15', $check_phone],
            'phone_code'        => ['nullable', 'numeric'],
        ];
    }
}
