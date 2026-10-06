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
        // Usuarios por defecto (cambia las contraseñas en producción)
        User::updateOrCreate(['email' => 'admin@sge.test'], [
            'name' => 'Administrador', 'password' => 'password', 'role' => 'admin',
        ]);
        User::updateOrCreate(['email' => 'vendedor@sge.test'], [
            'name' => 'Vendedor', 'password' => 'password', 'role' => 'vendedor',
        ]);

        foreach (['Efectivo', 'Tarjeta débito/crédito', 'Transferencia', 'Nequi / Daviplata'] as $name) {
            PaymentMethod::firstOrCreate(['name' => $name], ['active' => true]);
        }

        $cat = Category::firstOrCreate(['name' => 'General'], ['description' => 'Categoría inicial', 'active' => true]);

        Product::firstOrCreate(['name' => 'Producto de ejemplo'], [
            'description' => 'Producto demo', 'price' => 10000, 'stock' => 20,
            'category_id' => $cat->id, 'active' => true,
        ]);

        Client::firstOrCreate(['name' => 'Cliente mostrador'], ['active' => true]);
        Provider::firstOrCreate(['name' => 'Proveedor de ejemplo'], ['active' => true]);
    }
}
