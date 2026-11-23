<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['roles', 'permissions'])->orderBy('name')->get();
        $roles = Role::with('permissions')->where('guard_name', 'web')->orderBy('name')->get();
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        $moduleLabels = [
            'productos' => 'Productos',
            'categorias' => 'Categorías',
            'clientes' => 'Clientes',
            'proveedores' => 'Proveedores',
            'ventas' => 'Ventas',
            'compras' => 'Compras',
            'metodos-pago' => 'Métodos de pago',
            'inventario' => 'Inventario',
            'auditoria' => 'Auditoría',
            'administracion' => 'Administración',
        ];

        $permissionGroups = collect($moduleLabels)->mapWithKeys(fn ($label, $key) => [$key => collect()]);

        foreach ($permissions as $permission) {
            $name = $permission->name;

            $module = match (true) {
                str_ends_with($name, 'productos') => 'productos',
                str_ends_with($name, 'categorias') => 'categorias',
                str_ends_with($name, 'clientes') => 'clientes',
                str_ends_with($name, 'proveedores') => 'proveedores',
                str_ends_with($name, 'ventas'), $name === 'ver-reportes' => 'ventas',
                str_ends_with($name, 'compras') => 'compras',
                str_ends_with($name, 'metodos-pago') => 'metodos-pago',
                str_ends_with($name, 'inventario') => 'inventario',
                str_ends_with($name, 'auditoria') => 'auditoria',
                default => 'administracion',
            };

            $permissionGroups[$module]->push($permission);
        }

        $actions = [
            'ver' => 'Ver',
            'buscar' => 'Buscar',
            'crear' => 'Crear',
            'editar' => 'Editar',
            'eliminar' => 'Eliminar',
            'restaurar' => 'Restaurar',
            'recibir' => 'Recibir',
            'ajustar' => 'Ajustar',
            'gestionar' => 'Gestionar',
        ];

        $requestedModule = $request->query('module', 'todos');
        $moduleFilter = array_key_exists($requestedModule, $moduleLabels) || $requestedModule === 'todos'
            ? $requestedModule
            : 'todos';

        $requestedUserId = $request->query('user_id');
        $selectedUser = filled($requestedUserId)
            ? $users->firstWhere('id', (int) $requestedUserId)
            : null;

        return view('users.index', compact(
            'users',
            'roles',
            'permissions',
            'permissionGroups',
            'moduleLabels',
            'moduleFilter',
            'selectedUser',
            'actions'
        ));
    }

    public function updatePermissions(Request $request)
    {
        abort_unless($request->user()->hasRole('superadmin'), 403);

        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'role_permissions' => ['nullable', 'array'],
            'role_permissions.*' => ['nullable', 'array'],
            'role_permissions.*.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', 'web'),
            ],
            'direct_permissions' => ['nullable', 'array'],
            'direct_permissions.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', 'web'),
            ],
        ]);

        $roles = Role::where('guard_name', 'web')->get();

        $before = $roles->mapWithKeys(fn ($role) => [
            $role->name => $role->permissions()->pluck('name')->sort()->values()->all(),
        ])->all();

        $user = ! empty($data['user_id'])
            ? User::findOrFail($data['user_id'])
            : null;
        $beforeDirect = $user
            ? $user->getDirectPermissions()->pluck('name')->sort()->values()->all()
            : [];

        foreach ($roles as $role) {
            $role->syncPermissions($data['role_permissions'][$role->id] ?? []);
        }

        if ($user) {
            $user->syncPermissions($data['direct_permissions'] ?? []);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $after = $roles->mapWithKeys(fn ($role) => [
            $role->name => $role->fresh()->permissions()->pluck('name')->sort()->values()->all(),
        ])->all();

        $afterDirect = $user
            ? $user->getDirectPermissions()->pluck('name')->sort()->values()->all()
            : [];

        if ($before !== $after || $beforeDirect !== $afterDirect) {
            $activity = activity()
                ->causedBy($request->user())
                ->withProperties([
                    'permisos_por_rol_antes' => $before,
                    'permisos_por_rol_despues' => $after,
                    'permisos_directos_antes' => $beforeDirect,
                    'permisos_directos_despues' => $afterDirect,
                ]);

            if ($user) {
                $activity->performedOn($user);
            }

            $activity->log('Matriz de permisos actualizada');
        }

        $filters = ['module' => $request->input('module', 'todos')];

        if ($user) {
            $filters['user_id'] = $user->id;
        }

        return redirect()
            ->route('users.index', $filters)
            ->with('success', 'Matriz de permisos guardada.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->hasRole('superadmin'), 403);

        $data = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [
                'required',
                'string',
                'distinct',
                Rule::exists('roles', 'name')->where('guard_name', 'web'),
            ],
        ]);

        $canManagePermissions = $request->user()->can('gestionar-permisos');

        if ($canManagePermissions) {
            $permissionData = $request->validate([
                'permissions' => ['nullable', 'array'],
                'permissions.*' => [
                    'required',
                    'string',
                    'distinct',
                    Rule::exists('permissions', 'name')->where('guard_name', 'web'),
                ],
            ]);
        }

        $willRemainSuperadmin = in_array('superadmin', $data['roles'], true);

        if (
            $user->hasRole('superadmin')
            && ! $willRemainSuperadmin
            && User::role('superadmin')->count() <= 1
        ) {
            return back()->withErrors('No puedes quitar el rol al único superadministrador.');
        }

        $previousRoles = $user->roles()->pluck('name')->sort()->values()->all();
        $previousPermissions = $user->getDirectPermissions()->pluck('name')->sort()->values()->all();

        $user->syncRoles($data['roles']);

        if ($canManagePermissions) {
            $user->syncPermissions($permissionData['permissions'] ?? []);
        }

        $currentRoles = $user->roles()->pluck('name')->sort()->values()->all();
        $currentPermissions = $user->getDirectPermissions()->pluck('name')->sort()->values()->all();

        if ($previousRoles !== $currentRoles || $previousPermissions !== $currentPermissions) {
            activity()
                ->causedBy($request->user())
                ->performedOn($user)
                ->withProperties([
                    'roles_anteriores' => $previousRoles,
                    'roles_nuevos' => $currentRoles,
                    'permisos_anteriores' => $previousPermissions,
                    'permisos_nuevos' => $currentPermissions,
                ])
                ->log('Asignaciones de roles y permisos actualizadas');
        }

        return back()->with('success', 'Roles y permisos actualizados.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->hasRole('superadmin'), 403);

        if ($request->user()->is($user)) {
            return back()->withErrors([
                'user' => 'No puedes eliminar tu propia cuenta desde esta pantalla.',
            ]);
        }

        if ($user->hasRole('superadmin') && User::role('superadmin')->count() <= 1) {
            return back()->withErrors([
                'user' => 'No puedes eliminar al único superadministrador.',
            ]);
        }

        DB::transaction(function () use ($request, $user) {
            activity()
                ->causedBy($request->user())
                ->withProperties([
                    'usuario_id' => $user->id,
                    'nombre' => $user->name,
                    'correo' => $user->email,
                    'roles' => $user->getRoleNames()->all(),
                ])
                ->log('Cuenta de usuario eliminada');

            $user->tokens()->delete();
            $user->notifications()->delete();
            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->delete();
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'La cuenta fue eliminada correctamente.');
    }
}
