<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route is already gated by the pos middleware; keep true here.
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method'        => ['required', 'in:cash,card'],

            'items'                 => ['required', 'array', 'min:1'],
            'items.*.service_id'    => ['required', 'integer', 'exists:services,id'],
            'items.*.quantity'      => ['required', 'integer', 'min:1', 'max:10000'],
            // Optional per-transaction custom price. Basic shape check only —
            // CartCalculator is the authoritative validator/normalizer.
            // Zero is allowed (deliberate comp/free line, confirmed in the UI);
            // negatives are rejected here and again in the calculator.
            'items.*.custom_price'  => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'The cart is empty.',
            'items.min'      => 'The cart is empty.',
        ];
    }
}
