<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'active'  => ['nullable', 'boolean'],
        ];
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
        $data = $request->validate($this->rules());
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
        $data = $request->validate($this->rules());
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
