<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authentication;

use App\Modules\Identity\Contracts\AuthenticationManagerInterface;
use App\Modules\Identity\Contracts\AuthenticationProviderInterface;
use App\Modules\Identity\Exceptions\IdentityException;

/**
 * Registry of external authentication providers. Empty by default — the seam
 * through which OAuth/SSO/LDAP/AD/OIDC drivers are registered later.
 */
final class AuthenticationManager implements AuthenticationManagerInterface
{
    /** @var array<string, AuthenticationProviderInterface> */
    private array $providers = [];

    public function extend(AuthenticationProviderInterface $provider): void
    {
        $this->providers[$provider->name()] = $provider;
    }

    public function has(string $name): bool
    {
        return isset($this->providers[$name]);
    }

    public function provider(string $name): AuthenticationProviderInterface
    {
        if (! $this->has($name)) {
            throw new IdentityException("No authentication provider registered for [{$name}].");
        }

        return $this->providers[$name];
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        return array_keys($this->providers);
    }
}
