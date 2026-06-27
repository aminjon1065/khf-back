<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Services;

use App\Models\User;
use App\Modules\Identity\Contracts\IdentityServiceInterface;
use App\Modules\Navigation\Contracts\NavigationVisibilityEvaluatorInterface;
use App\Modules\Navigation\Enums\NavigationVisibility;

/**
 * Maps each visibility mode to an identity check. Permission checks go through
 * the Gate (IdentityServiceInterface::can) so the Super Admin wildcard is
 * honoured; role checks use the literal role test. Rules are OR-combined.
 */
final class NavigationVisibilityEvaluator implements NavigationVisibilityEvaluatorInterface
{
    public function __construct(private readonly IdentityServiceInterface $identity) {}

    /**
     * @param  list<string>  $rules
     */
    public function isVisible(NavigationVisibility $visibility, array $rules, ?User $user): bool
    {
        return match ($visibility) {
            NavigationVisibility::Public => true,
            NavigationVisibility::Authenticated => $user !== null,
            NavigationVisibility::Roles => $this->matches(
                $user,
                $rules,
                fn (User $viewer, string $role): bool => $this->identity->hasRole($viewer, $role),
            ),
            NavigationVisibility::Permissions => $this->matches(
                $user,
                $rules,
                fn (User $viewer, string $permission): bool => $this->identity->can($viewer, $permission),
            ),
        };
    }

    /**
     * @param  list<string>  $rules
     * @param  callable(User, string): bool  $check
     */
    private function matches(?User $user, array $rules, callable $check): bool
    {
        if ($user === null) {
            return false;
        }

        foreach ($rules as $rule) {
            if ($check($user, $rule)) {
                return true;
            }
        }

        return false;
    }
}
