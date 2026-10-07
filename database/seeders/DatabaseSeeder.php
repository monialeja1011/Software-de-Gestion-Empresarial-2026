<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primero crea los roles y permisos de Spatie.
        $this->call(RolePermissionSeeder::class);

        $admin = User::updateOrCreate(
            ['email' => 'admin@sge.test'],
            ['name' => 'Administrador', 'password' => '123456789']
        );
        $admin->syncRoles(['admin']);

        $vendedor = User::updateOrCreate(
            ['email' => 'vendedor@sge.test'],
            ['name' => 'Vendedor', 'password' => '123456789']
        );
        $vendedor->syncRoles(['vendedor']);

        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@sge.test'],
            ['name' => 'Superadministrador', 'password' => '123456789']
        );
        $superadmin->syncRoles(['superadmin']);

        foreach ([
            'Efectivo',
            'Tarjeta débito/crédito',
            'Transferencia',
            'Nequi / Daviplata',
        ] as $name) {
            PaymentMethod::firstOrCreate(
                ['name' => $name],
                ['active' => true]
            );
        }

        $category = Category::firstOrCreate(
            ['name' => 'General'],
            ['description' => 'Categoría inicial', 'active' => true]
        );

        Product::firstOrCreate(
            ['name' => 'Producto de ejemplo'],
            [
                'description' => 'Producto demo',
                'price' => 10000,
                'stock' => 20,
                'category_id' => $category->id,
                'active' => true,
            ]
        );

        Client::firstOrCreate(
            ['name' => 'Cliente mostrador'],
            ['active' => true]
        );

        Provider::firstOrCreate(
            ['name' => 'Proveedor de ejemplo'],
            ['active' => true]
        );
    }
}