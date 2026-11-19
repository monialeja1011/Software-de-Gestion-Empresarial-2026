<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ApiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@test.com'],
            [
                'name' => 'Supervisor API',
                'password' => 'password',
            ]
        );

        $supervisor->syncRoles(['vendedor', 'almacenista']);
    }
}
