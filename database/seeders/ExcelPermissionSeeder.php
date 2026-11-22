<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ExcelPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect([
            'exportar-productos',
            'importar-productos',
            'exportar-ventas',
        ])->map(fn (string $name) => Permission::findOrCreate($name, 'web'));

        $assignments = [
            'superadmin' => $permissions->all(),
            'admin' => $permissions->all(),
            'almacenista' => $permissions->whereIn('name', [
                'exportar-productos',
                'importar-productos',
            ])->all(),
            'vendedor' => $permissions->where('name', 'exportar-ventas')->all(),
        ];

        foreach ($assignments as $roleName => $rolePermissions) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role) {
                $role->givePermissionTo($rolePermissions);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
