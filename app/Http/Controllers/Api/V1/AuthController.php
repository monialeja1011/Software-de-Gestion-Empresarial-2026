<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
            'role' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        $roles = $user->roles()->orderBy('name')->get();
        $roleNames = $roles->pluck('name')->all();

        if ($roles->isEmpty()) {
            throw ValidationException::withMessages([
                'role' => ['El usuario no tiene roles asignados.'],
            ]);
        }

        if (count($roleNames) > 1 && empty($data['role'])) {
            throw ValidationException::withMessages([
                'role' => [
                    'Tu usuario tiene varios roles. Indica con cuál deseas trabajar: '
                    .implode(', ', $roleNames).'.',
                ],
            ]);
        }

        $activeRole = $data['role'] ?? $roleNames[0];

        if (! in_array($activeRole, $roleNames, true)) {
            throw ValidationException::withMessages([
                'role' => ['El rol indicado no está asignado a este usuario.'],
            ]);
        }

        $token = $user->createToken(
            $data['device_name'],
            ['rol:'.$activeRole]
        );

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'rol_activo' => $activeRole,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ], 201);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        $roleAbility = $token instanceof PersonalAccessToken
            ? collect($token->abilities)->first(
                fn ($ability) => is_string($ability) && str_starts_with($ability, 'rol:')
            )
            : null;

        $roleName = $roleAbility ? substr($roleAbility, 4) : null;
        $activeRole = $roleName
            ? $user->roles()->where('name', $roleName)->with('permissions')->first()
            : null;

        $permissions = $user->getDirectPermissions()->pluck('name');

        if ($activeRole) {
            $permissions = $permissions->merge(
                $activeRole->permissions->pluck('name')
            );
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol_activo' => $activeRole?->name,
                'roles' => $user->getRoleNames()->values(),
                'permisos' => $permissions->unique()->sort()->values(),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
