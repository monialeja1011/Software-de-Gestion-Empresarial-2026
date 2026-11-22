<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    private function rules(?Client $client = null): array
    {
        return [
            'name'            => ['required', 'string', 'max:150'],
            'document_number' => [
                'required',
                'string',
                'regex:/^[0-9]{5,15}$/',
                Rule::unique('clients', 'document_number')->ignore($client?->id),
            ],
            'email'           => ['nullable', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:255'],
            'address'         => ['nullable', 'string', 'max:255'],
            'active'          => ['nullable', 'boolean'],
        ];
    }

    private function messages(): array
    {
        return [
            'document_number.regex'  => 'La cédula solo debe tener números (entre 5 y 15 dígitos).',
            'document_number.unique' => 'Ya existe un cliente registrado con esa cédula.',
        ];
    }

    private function attributes(): array
    {
        return ['document_number' => 'cédula'];
    }

    /** Deja solo los dígitos: acepta "1.088.234.567" o "1 088 234 567". */
    private function normalizeDocument(Request $request): void
    {
        if ($request->has('document_number')) {
            $request->merge([
                'document_number' => preg_replace('/\D/', '', (string) $request->input('document_number')),
            ]);
        }
    }

    public function index()
    {
        $clients = Client::orderBy('name')->paginate(15);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $this->normalizeDocument($request);

        $data = $request->validate($this->rules(), $this->messages(), $this->attributes());
        $data['active'] = $request->boolean('active');
        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Cliente creado.');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->normalizeDocument($request);

        $data = $request->validate($this->rules($client), $this->messages(), $this->attributes());
        $data['active'] = $request->boolean('active');
        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado.');
    }

    public function destroy(Client $client)
    {
        if ($client->sales()->exists()) {
            return back()->withErrors('No se puede eliminar: tiene ventas registradas. Puedes desactivarlo.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente eliminado.');
    }
}
