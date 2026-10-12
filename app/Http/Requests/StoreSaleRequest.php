<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('active', true),
            ],
            'payment_method_id' => [
                'required',
                Rule::exists('payment_methods', 'id')->where('active', true),
            ],
            'sale_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('active', true)
                    ->whereNull('deleted_at'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'client_id' => 'cliente',
            'payment_method_id' => 'método de pago',
            'sale_date' => 'fecha de venta',
            'items' => 'productos',
            'items.*.product_id' => 'producto',
            'items.*.quantity' => 'cantidad',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Debes agregar al menos un producto a la venta.',
            'items.min' => 'Debes agregar al menos un producto a la venta.',
        ];
    }
}