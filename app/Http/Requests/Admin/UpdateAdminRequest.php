<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        // $id = $this->route('admin');
        return [
            'name' => ['required', 'max:255'],
            // 'phone' => ['required', 'digits_between:9,15', 'numeric'],
            'email' => ['required', 'unique:admins,email,' . $request->admin . ',id'],
            'role' => ['required', 'exists:roles,name']
        ];
    }
}
