<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;

use App\Models\CompanySetting;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('branding', CompanySetting::current());
        });
        Gate::after(function ($user, $ability, $result, $arguments) {
            if (! $user instanceof User || ! is_string($ability)) {
                return null;
            }

            $permission = Permission::query()
                ->where('name', $ability)
                ->where('guard_name', 'web')
                ->first();

            // Para capacidades que no son permisos de Spatie,
            // se conserva el resultado normal de Laravel.
            if (! $permission) {
                return null;
            }

            // Un permiso directo asignado al usuario sigue siendo válido.
            if ($user->getDirectPermissions()->contains('id', $permission->id)) {
                return true;
            }

            $activeRoleId = session('active_role_id');

            if (! $activeRoleId) {
                $token = $user->currentAccessToken();

                if ($token instanceof PersonalAccessToken) {
                    $roleAbility = collect($token->abilities)->first(
                        fn ($ability) => is_string($ability) && str_starts_with($ability, 'rol:')
                    );

                    if ($roleAbility) {
                        $roleName = substr($roleAbility, 4);
                        $activeRoleId = $user->roles()
                            ->where('name', $roleName)
                            ->value('roles.id');
                    }
                }
            }

            if (! $activeRoleId) {
                return false;
            }

            // Los permisos de roles distintos al activo no se aplican
            // durante esta sesión.
            return $user->roles()
                ->whereKey($activeRoleId)
                ->whereHas('permissions', function ($query) use ($permission) {
                    $query->whereKey($permission->id);
                })
                ->exists();
        });
    }
}
