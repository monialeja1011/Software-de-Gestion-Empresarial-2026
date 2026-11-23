<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    private ?Client $documentClient = null;

    private bool $documentClientLoaded = false;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * La pantalla web identifica al cliente por su cédula; la API sigue
     * enviando client_id, así que ese camino se conserva igual.
     */
    private function usesDocument(): bool
    {
        return ! $this->filled('client_id');
    }

    private function clientByDocument(): ?Client
    {
        if (! $this->documentClientLoaded) {
            $this->documentClient = Client::where('document_number', (string) $this->input('client_document'))->first();
            $this->documentClientLoaded = true;
        }

        return $this->documentClient;
    }

    /** Deja solo los dígitos: acepta "1.088.234.567" o "1 088 234 567". */
    protected function prepareForValidation(): void
    {
        if ($this->has('client_document')) {
            $this->merge([
                'client_document' => preg_replace('/\D/', '', (string) $this->input('client_document')),
            ]);
        }
    }

    public function rules(): array
    {
        $client = $this->usesDocument()
            ? [
                'client_document' => ['required', 'string', 'regex:/^[0-9]{5,15}$/'],
                // El nombre solo es obligatorio cuando la cédula aún no está registrada.
                'client_name' => [
                    Rule::requiredIf(fn () => $this->clientByDocument() === null),
                    'nullable',
                    'string',
                    'max:150',
                ],
                // Datos de contacto: se piden solo cuando la cédula aún no está registrada.
                'client_phone' => [
                    Rule::requiredIf(fn () => $this->clientByDocument() === null),
                    'nullable',
                    'string',
                    'regex:/^[0-9+\-\s()]{7,20}$/',
                ],
                'client_email' => [
                    Rule::requiredIf(fn () => $this->clientByDocument() === null),
                    'nullable',
                    'email',
                    'max:255',
                ],
            ]
            : [
                'client_id' => [
                    'required',
                    Rule::exists('clients', 'id')->where('active', true),
                ],
            ];

        return $client + [
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->usesDocument()) {
                return;
            }

            $client = $this->clientByDocument();

            if ($client && ! $client->active) {
                $validator->errors()->add(
                    'client_document',
                    'Ese cliente está inactivo. Actívalo en Clientes antes de venderle.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'client_id' => 'cliente',
            'client_document' => 'cédula',
            'client_name' => 'nombre del cliente',
            'client_phone' => 'teléfono',
            'client_email' => 'correo electrónico',
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
            'client_document.regex' => 'La cédula solo debe tener números (entre 5 y 15 dígitos).',
            'client_name.required' => 'Escribe el nombre del cliente: esa cédula aún no está registrada.',
            'client_phone.required' => 'Escribe el teléfono del cliente nuevo.',
            'client_phone.regex' => 'El teléfono solo puede tener números, espacios, + y guiones (entre 7 y 20 caracteres).',
            'client_email.required' => 'Escribe el correo electrónico del cliente nuevo.',
            'client_email.email' => 'Escribe un correo electrónico válido.',
            'items.required' => 'Debes agregar al menos un producto a la venta.',
            'items.min' => 'Debes agregar al menos un producto a la venta.',
        ];
    }
}
