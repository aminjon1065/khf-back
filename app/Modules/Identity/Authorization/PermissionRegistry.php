<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authorization;

use App\Modules\Identity\Contracts\PermissionRegistryInterface;

final class PermissionRegistry implements PermissionRegistryInterface
{
    /** @var list<string> */
    private array $permissions;

    /**
     * @param  list<string>  $permissions
     */
    public function __construct(array $permissions = [])
    {
        $this->permissions = array_values(array_unique($permissions));
    }

    public function register(string $permission): void
    {
        if (! $this->has($permission)) {
            $this->permissions[] = $permission;
        }
    }

    public function has(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    /**
     * @return list<string>
     */
    public function all(): array
    {
        return $this->permissions;
    }
}
