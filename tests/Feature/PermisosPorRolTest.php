<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PermisosPorRolTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
    }

    public static function matriz(): array
    {
        return [
            'admin ventas' => ['admin', 'sales.index', 200],
            'admin compras' => ['admin', 'purchases.index', 200],
            'admin inventario' => ['admin', 'inventory.index', 200],
            'admin auditoria' => ['admin', 'audit.index', 200],
            'admin identidad' => ['admin', 'company-settings.edit', 200],
            'vendedor crear venta' => ['vendedor', 'sales.create', 200],
            'vendedor ver compras' => ['vendedor', 'purchases.index', 403],
            'vendedor ver inventario' => ['vendedor', 'inventory.index', 403],
            'vendedor ver auditoria' => ['vendedor', 'audit.index', 403],
            'vendedor ver identidad' => ['vendedor', 'company-settings.edit', 403],
            'almacenista crear compra' => ['almacenista', 'purchases.create', 200],
            'almacenista ver inventario' => ['almacenista', 'inventory.index', 200],
            'almacenista ver ventas' => ['almacenista', 'sales.index', 403],
            'almacenista ver auditoria' => ['almacenista', 'audit.index', 403],
            'almacenista ver identidad' => ['almacenista', 'company-settings.edit', 403],
        ];
    }

    #[DataProvider('matriz')]
    public function test_acceso_segun_rol(string $roleName, string $routeName, int $expected): void
    {
        $user = User::factory()->create();
        $user->assignRole($roleName);
        $roleId = (int) $user->roles()->first()->id;

        $this->actingAs($user)
            ->withSession(['active_role_id' => $roleId])
            ->get(route($routeName))
            ->assertStatus($expected);
    }

    public function test_usuario_invitado_es_enviado_al_login(): void
    {
        $this->get(route('sales.index'))->assertRedirect(route('login'));
    }
}
