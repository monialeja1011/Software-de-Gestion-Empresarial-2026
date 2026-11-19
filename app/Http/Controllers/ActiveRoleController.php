<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActiveRoleController extends Controller
{
    public function edit(Request $request)
    {
        $roles = $request->user()->roles()->orderBy('name')->get();

        abort_if($roles->isEmpty(), 403, 'Tu usuario no tiene roles asignados.');

        if ($roles->count() === 1) {
            $request->session()->put('active_role_id', $roles->first()->id);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return view('auth.active-role', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer'],
        ]);

        // Solo permite seleccionar un rol que esté asignado a este usuario.
        $role = $request->user()->roles()
            ->whereKey($data['role_id'])
            ->first();

        abort_unless($role, 403, 'No tienes asignado ese rol.');

        $request->session()->put('active_role_id', $role->id);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
