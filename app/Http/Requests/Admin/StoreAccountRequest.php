<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class StoreAccountRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:admins,email'],
            // Strong password on creation.
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'role'     => ['required', 'in:admin,cashier'],
        ];
    }
}
