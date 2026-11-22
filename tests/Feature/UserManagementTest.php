<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function createUserWithRole(string $role): User
    {
        Role::findOrCreate($role, 'web');

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_superadmin_can_delete_another_account(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');
        $target = $this->createUserWithRole('vendedor');
        $targetId = $target->id;

        $this->actingAs($superadmin)
            ->delete(route('users.destroy', $target))
            ->assertRedirect(route('users.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('users', ['id' => $targetId]);
        $this->assertDatabaseHas('activity_log', [
            'description' => 'Cuenta de usuario eliminada',
            'causer_id' => $superadmin->id,
        ]);
    }

    public function test_admin_cannot_delete_another_account(): void
    {
        $admin = $this->createUserWithRole('admin');
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $target))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_superadmin_cannot_delete_their_own_account(): void
    {
        $superadmin = $this->createUserWithRole('superadmin');

        $this->actingAs($superadmin)
            ->from(route('users.index'))
            ->delete(route('users.destroy', $superadmin))
            ->assertSessionHasErrors('user')
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['id' => $superadmin->id]);
    }
}
