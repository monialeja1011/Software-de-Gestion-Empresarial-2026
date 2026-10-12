<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $names = [
            'ver-productos',
            'buscar-productos',
            'crear-productos',
            'editar-productos',
            'eliminar-productos',
            'restaurar-productos',

            'ver-categorias',
            'buscar-categorias',
            'crear-categorias',
            'editar-categorias',
            'eliminar-categorias',
            'restaurar-categorias',

            'ver-clientes',
            'buscar-clientes',
            'crear-clientes',
            'editar-clientes',
            'eliminar-clientes',
            'restaurar-clientes',

            'ver-proveedores',
            'buscar-proveedores',
            'crear-proveedores',
            'editar-proveedores',
            'eliminar-proveedores',

            'ver-ventas',
            'buscar-ventas',
            'crear-ventas',
            'editar-ventas',
            'eliminar-ventas',
            'ver-reportes',

            'ver-compras',
            'buscar-compras',
            'crear-compras',
            'editar-compras',
            'eliminar-compras',

            'ver-metodos-pago',
            'crear-metodos-pago',
            'editar-metodos-pago',
            'eliminar-metodos-pago',

            'gestionar-usuarios',
            'gestionar-roles',
            'gestionar-permisos',
        ];

        foreach ($names as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $vendedor = Role::findOrCreate('vendedor', 'web');
        $superadmin = Role::findOrCreate('superadmin', 'web');
        $almacenista = Role::findOrCreate('almacenista', 'web');

        // El admin administra la operación y los usuarios.
        // Solo el superadmin gestiona roles y permisos.
        $admin->syncPermissions(
            Permission::whereNotIn('name', [
                'gestionar-roles',
                'gestionar-permisos',
            ])->get()
        );

        $vendedor->syncPermissions([
            'ver-productos',
            'buscar-productos',
            'ver-clientes',
            'buscar-clientes',
            'crear-clientes',
            'ver-ventas',
            'buscar-ventas',
            'crear-ventas',
        ]);

        $almacenista->syncPermissions([
            'ver-productos',
            'buscar-productos',
            'crear-productos',
            'editar-productos',
            'ver-categorias',
            'buscar-categorias',
            'ver-compras',
            'crear-compras',
        ]);

        $superadmin->syncPermissions(Permission::all());
    }
}