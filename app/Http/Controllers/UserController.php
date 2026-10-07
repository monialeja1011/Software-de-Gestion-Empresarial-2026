<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'vendedor', 'superadmin'])],
        ]);

        // Evita dejar el sistema sin ningún superadmin.
        if (
            $user->hasRole('superadmin')
            && $data['role'] !== 'superadmin'
            && User::role('superadmin')->count() <= 1
        ) {
            return back()->withErrors(
                'No puedes quitar el rol al único superadmin.'
            );
        }

        // Cambia la relación de Spatie, no una columna en users.
        $user->syncRoles([$data['role']]);

        return back()->with('success', 'Rol actualizado.');
    }
}