<?php

declare(strict_types=1);

namespace App\Modules\Identity;

use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\ModuleInterface;
use App\Models\User;
use App\Modules\Identity\Authentication\AuthenticationManager;
use App\Modules\Identity\Authorization\PermissionRegistry;
use App\Modules\Identity\Authorization\Permissions;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Contracts\AuthenticationManagerInterface;
use App\Modules\Identity\Contracts\IdentityServiceInterface;
use App\Modules\Identity\Contracts\PermissionRegistryInterface;
use App\Modules\Identity\Contracts\UserRepositoryInterface;
use App\Modules\Identity\Listeners\HandleFailedLogin;
use App\Modules\Identity\Listeners\HandleLogout;
use App\Modules\Identity\Listeners\HandlePasswordReset;
use App\Modules\Identity\Listeners\HandleRegistered;
use App\Modules\Identity\Listeners\HandleSuccessfulLogin;
use App\Modules\Identity\Models\Role;
use App\Modules\Identity\Policies\RolePolicy;
use App\Modules\Identity\Policies\UserPolicy;
use App\Modules\Identity\Repositories\EloquentUserRepository;
use App\Modules\Identity\Services\ActivityLogger;
use App\Modules\Identity\Services\IdentityService;
use App\Modules\Identity\Support\IdentityHooks;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;

/**
 * Bootstraps the Identity & Access Management module — the single source of
 * truth for authentication and authorization. Registered in config/khf.php.
 */
final class IdentityModule implements ModuleInterface
{
    public function __construct(private readonly Application $app) {}

    public function register(): void
    {
        $this->app->singleton(ActivityLoggerInterface::class, ActivityLogger::class);
        $this->app->singleton(IdentityServiceInterface::class, IdentityService::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->singleton(AuthenticationManagerInterface::class, AuthenticationManager::class);

        $this->app->singleton(PermissionRegistryInterface::class, function (): PermissionRegistry {
            $permissions = Permissions::all();
            $filtered = $this->app->make(HookManagerInterface::class)
                ->applyFilters(IdentityHooks::REGISTER_PERMISSIONS, $permissions);

            return new PermissionRegistry(
                is_array($filtered) ? array_values(array_map(strval(...), $filtered)) : $permissions,
            );
        });
    }

    public function boot(): void
    {
        // Super Admin can do everything (wildcard short-circuit).
        Gate::before(function (mixed $user, string $ability): ?bool {
            return $user instanceof User && $user->hasRole(Roles::SUPER_ADMIN) ? true : null;
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        Event::listen(Registered::class, HandleRegistered::class);
        Event::listen(Login::class, HandleSuccessfulLogin::class);
        Event::listen(Logout::class, HandleLogout::class);
        Event::listen(Failed::class, HandleFailedLogin::class);
        Event::listen(PasswordReset::class, HandlePasswordReset::class);
    }
}
