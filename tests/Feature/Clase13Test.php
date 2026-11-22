<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class Clase13Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_api_requires_a_token(): void
    {
        $this->getJson('/api/v1/products')->assertUnauthorized();
    }

    public function test_login_returns_a_bearer_token_with_the_active_role(): void
    {
        $user = $this->userWithPermissions('vendedor', ['ver-productos']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'clave-de-prueba',
            'device_name' => 'prueba-clase-13',
            'role' => 'vendedor',
        ]);

        $response->assertCreated();

        $token = data_get($response->json(), 'data.token')
            ?? data_get($response->json(), 'token');

        $this->assertNotEmpty($token);
        $response->assertJsonFragment(['rol_activo' => 'vendedor']);
    }

    public function test_token_can_access_the_current_user_endpoint(): void
    {
        $user = $this->userWithPermissions('vendedor', ['ver-productos']);
        $token = $user->createToken('prueba-clase-13', ['rol:vendedor'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonFragment(['email' => $user->email])
            ->assertJsonFragment(['rol_activo' => 'vendedor']);
    }

    public function test_user_with_product_permission_can_list_products(): void
    {
        $user = $this->userWithPermissions('vendedor', ['ver-productos']);
        $token = $user->createToken('prueba-clase-13', ['rol:vendedor'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/products')
            ->assertOk();
    }

    public function test_user_without_sales_permission_receives_forbidden(): void
    {
        $user = $this->userWithPermissions('almacenista', []);
        $token = $user->createToken('prueba-clase-13', ['rol:almacenista'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/sales', [])
            ->assertForbidden();
    }

    public function test_invalid_sale_data_returns_unprocessable_entity(): void
    {
        $user = $this->userWithPermissions('vendedor', ['crear-ventas']);
        $token = $user->createToken('prueba-clase-13', ['rol:vendedor'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/sales', [])
            ->assertUnprocessable();
    }

    private function userWithPermissions(string $roleName, array $permissionNames): User
    {
        $permissions = collect($permissionNames)
            ->map(fn (string $name) => Permission::findOrCreate($name, 'web'));

        $role = Role::findOrCreate($roleName, 'web');
        $role->syncPermissions($permissions);

        $user = User::factory()->create([
            'password' => 'clave-de-prueba',
        ]);

        $user->assignRole($role);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }
}
