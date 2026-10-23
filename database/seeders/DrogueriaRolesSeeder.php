<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DrogueriaRolesSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $names = [
            'ver-lotes',
            'crear-lotes',
            'editar-lotes',
            'recibir-lotes',
            'revisar-lotes',
            'crear-compras',
            'ver-compras',
            'recibir-compras',
            'ver-ventas',
            'crear-ventas',
            'dispensar-control-especial',
            'autorizar-control-especial',
            'ver-productos',
            'buscar-productos',
        ];

        foreach ($names as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $regente = Role::findOrCreate('regente', 'web');
        $cajero = Role::findOrCreate('cajero', 'web');
        $bodega = Role::findOrCreate('auxiliar-bodega', 'web');

        $admin->syncPermissions(
            Permission::whereNotIn('name', [
                'gestionar-roles',
                'gestionar-permisos',
            ])->get()
        );

        $regente->syncPermissions([
            'ver-productos',
            'buscar-productos',
            'ver-lotes',
            'ver-compras',
            'revisar-lotes',
            'ver-ventas',
            'autorizar-control-especial',
        ]);

        $cajero->syncPermissions([
            'ver-productos',
            'buscar-productos',
            'ver-ventas',
            'crear-ventas',
            'dispensar-control-especial',
        ]);

        $bodega->syncPermissions([
            'ver-productos',
            'buscar-productos',
            'ver-lotes',
            'ver-compras',
            'recibir-lotes',
        ]);
    }
}