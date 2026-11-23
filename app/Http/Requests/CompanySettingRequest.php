<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanySettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('editar-identidad') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $website = trim((string) $this->input('website'));

        if ($website !== '' && ! preg_match('#^https?://#i', $website)) {
            $this->merge(['website' => 'https://'.$website]);
        }

        if ($this->filled('brand_color')) {
            $this->merge(['brand_color' => strtolower(trim($this->input('brand_color')))]);
        }
    }

    public function rules(): array
    {
        return [
            'trade_name' => ['required', 'string', 'max:120'],
            'legal_name' => ['nullable', 'string', 'max:160'],
            'nit' => ['nullable', 'string', 'regex:/^[0-9][0-9.\-]{4,19}$/'],
            'slogan' => ['nullable', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:200'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url:http,https', 'max:200'],
            'brand_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'trade_name' => 'nombre comercial',
            'legal_name' => 'razón social',
            'nit' => 'NIT',
            'slogan' => 'eslogan',
            'address' => 'dirección',
            'city' => 'ciudad',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'website' => 'sitio web',
            'brand_color' => 'color principal',
            'logo' => 'logo',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max.string' => 'El campo :attribute no puede superar :max caracteres.',
            'max.file' => 'El logo no puede pesar más de 2 MB.',
            'email' => 'Escribe un correo electrónico válido.',
            'url' => 'Escribe un sitio web válido, por ejemplo www.empresa.com.',
            'nit.regex' => 'El NIT solo puede tener números, puntos y guion. Ejemplo: 900.123.456-7.',
            'phone.regex' => 'El teléfono solo puede tener números, espacios, +, - y paréntesis.',
            'brand_color.regex' => 'El color debe tener el formato #RRGGBB. Ejemplo: #db2777.',
            'image' => 'El logo debe ser una imagen.',
            'mimes' => 'El logo debe ser PNG, JPG o WebP.',
            'uploaded' => 'No se pudo subir el logo. Intenta con una imagen más liviana.',
        ];
    }
}
