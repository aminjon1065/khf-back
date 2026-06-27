<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Models\User;
use App\Modules\Navigation\Enums\NavigationVisibility;

/**
 * Decides whether a navigation item is visible to a given viewer (or guest when
 * $user is null), per its visibility mode and rule set.
 */
interface NavigationVisibilityEvaluatorInterface
{
    /**
     * @param  list<string>  $rules  role or permission names, depending on the mode
     */
    public function isVisible(NavigationVisibility $visibility, array $rules, ?User $user): bool;
}
