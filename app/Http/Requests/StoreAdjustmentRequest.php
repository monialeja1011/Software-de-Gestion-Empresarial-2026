<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('ajustar-inventario') ?? false;
    }

    public function rules(): array
    {
        return [
            'counted_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }
}
