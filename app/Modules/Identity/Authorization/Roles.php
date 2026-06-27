<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authorization;

/**
 * Canonical system roles and their default permission sets. These seven roles
 * are flagged is_system (immutable). Super Admin is granted everything via a
 * Gate::before wildcard, so its permission list here is illustrative.
 * Plugins add roles via the REGISTER_ROLES hook; admins add custom roles via
 * the IdentityService.
 */
final class Roles
{
    public const string SUPER_ADMIN = 'super-admin';

    public const string ADMINISTRATOR = 'administrator';

    public const string EDITOR = 'editor';

    public const string PUBLISHER = 'publisher';

    public const string REVIEWER = 'reviewer';

    public const string MODERATOR = 'moderator';

    public const string VIEWER = 'viewer';

    /**
     * Role => permission names. Super Admin is intentionally omitted from
     * explicit grants (it is all-powerful via the Gate::before wildcard).
     *
     * @return array<string, list<string>>
     */
    public static function definitions(): array
    {
        return [
            self::ADMINISTRATOR => [
                Permissions::ENTRIES_VIEW, Permissions::ENTRIES_CREATE, Permissions::ENTRIES_UPDATE,
                Permissions::ENTRIES_PUBLISH, Permissions::ENTRIES_DELETE,
                Permissions::MEDIA_VIEW, Permissions::MEDIA_UPLOAD, Permissions::MEDIA_DELETE,
                Permissions::USERS_VIEW, Permissions::USERS_CREATE, Permissions::USERS_UPDATE, Permissions::USERS_DELETE,
                Permissions::ROLES_MANAGE, Permissions::ACTIVITY_VIEW,
                Permissions::SETTINGS_MANAGE, Permissions::WORKFLOW_APPROVE, Permissions::SEARCH_REINDEX,
            ],
            self::EDITOR => [
                Permissions::ENTRIES_VIEW, Permissions::ENTRIES_CREATE, Permissions::ENTRIES_UPDATE,
                Permissions::MEDIA_VIEW, Permissions::MEDIA_UPLOAD,
            ],
            self::PUBLISHER => [
                Permissions::ENTRIES_VIEW, Permissions::ENTRIES_PUBLISH, Permissions::MEDIA_VIEW,
            ],
            self::REVIEWER => [
                Permissions::ENTRIES_VIEW, Permissions::WORKFLOW_APPROVE, Permissions::ACTIVITY_VIEW,
            ],
            self::MODERATOR => [
                Permissions::ENTRIES_VIEW, Permissions::ENTRIES_UPDATE,
                Permissions::MEDIA_VIEW, Permissions::MEDIA_DELETE, Permissions::USERS_VIEW,
            ],
            self::VIEWER => [
                Permissions::ENTRIES_VIEW, Permissions::MEDIA_VIEW,
                Permissions::USERS_VIEW, Permissions::ACTIVITY_VIEW,
            ],
        ];
    }

    /**
     * Every system role name (Super Admin included).
     *
     * @return list<string>
     */
    public static function systemRoles(): array
    {
        return [self::SUPER_ADMIN, ...array_keys(self::definitions())];
    }
}
