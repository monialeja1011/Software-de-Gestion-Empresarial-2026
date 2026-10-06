<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
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
        $providers = Provider::orderBy('name')->paginate(15);
        return view('providers.index', compact('providers'));
    }

    public function create()
    {
        return view('providers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['active'] = $request->boolean('active');
        Provider::create($data);

        return redirect()->route('providers.index')->with('success', 'Proveedor creado.');
    }

    public function edit(Provider $provider)
    {
        return view('providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $data = $request->validate($this->rules());
        $data['active'] = $request->boolean('active');
        $provider->update($data);

        return redirect()->route('providers.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Provider $provider)
    {
        if ($provider->purchases()->exists()) {
            return back()->withErrors('No se puede eliminar: tiene compras registradas. Puedes desactivarlo.');
        }

        $provider->delete();

        return redirect()->route('providers.index')->with('success', 'Proveedor eliminado.');
    }
}
