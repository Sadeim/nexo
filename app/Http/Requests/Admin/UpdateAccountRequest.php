<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class UpdateAccountRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('account');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:admins,email,' . $id . ',id'],
            // Password optional on edit; if provided it must be strong.
            'password' => ['nullable', Password::min(8)->letters()->numbers()],
            'role'     => ['required', 'in:admin,cashier'],
        ];
    }
}
