<?php

declare(strict_types=1);

namespace App\Modules\Identity\Contracts;

use App\Models\User;

/**
 * Extension point for external identity providers (OAuth, SSO/SAML, OIDC, LDAP,
 * Active Directory). Future drivers implement this and register with the
 * AuthenticationManager — no concrete provider ships in this sprint.
 */
interface AuthenticationProviderInterface
{
    public function name(): string;

    /**
     * Authenticate against the provider and resolve (or provision) the local
     * user, or null if authentication fails.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function authenticate(array $credentials): ?User;
}
