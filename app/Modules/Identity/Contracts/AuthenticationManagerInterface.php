<?php

declare(strict_types=1);

namespace App\Modules\Identity\Contracts;

use App\Modules\Identity\Exceptions\IdentityException;

/**
 * Registry of external authentication providers. The seam through which OAuth/
 * SSO/LDAP/AD/OIDC drivers are added later without changing core auth.
 */
interface AuthenticationManagerInterface
{
    public function extend(AuthenticationProviderInterface $provider): void;

    public function has(string $name): bool;

    /**
     * @throws IdentityException when no provider is registered under $name
     */
    public function provider(string $name): AuthenticationProviderInterface;

    /**
     * @return list<string>
     */
    public function names(): array;
}
