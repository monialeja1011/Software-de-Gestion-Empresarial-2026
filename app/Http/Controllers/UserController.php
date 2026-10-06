<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(['admin', 'vendedor'])]]);

        // Evita que el último administrador se quite a sí mismo el rol
        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->withErrors('No puedes quitarte tu propio rol de administrador.');
        }

        $user->update($data);

        return back()->with('success', 'Rol actualizado.');
    }
}
