<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Enums;

/**
 * Visibility modes for a navigation item. The NavigationVisibilityEvaluator maps
 * each mode to an identity check; the per-item role/permission names live in the
 * item's `visibility_rules`.
 */
enum NavigationVisibility: string
{
    case Public = 'public';
    case Authenticated = 'authenticated';
    case Roles = 'roles';
    case Permissions = 'permissions';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $visibility): string => $visibility->value, self::cases());
    }
}
