<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authorization;

/**
 * Canonical permission catalogue. The single source of truth for granular
 * permissions — no module hardcodes permission strings outside this class.
 * Plugins extend the catalogue via the REGISTER_PERMISSIONS hook.
 */
final class Permissions
{
    public const string ENTRIES_VIEW = 'entries.view';

    public const string ENTRIES_CREATE = 'entries.create';

    public const string ENTRIES_UPDATE = 'entries.update';

    public const string ENTRIES_PUBLISH = 'entries.publish';

    public const string ENTRIES_DELETE = 'entries.delete';

    public const string MEDIA_VIEW = 'media.view';

    public const string MEDIA_UPLOAD = 'media.upload';

    public const string MEDIA_DELETE = 'media.delete';

    public const string USERS_VIEW = 'users.view';

    public const string USERS_CREATE = 'users.create';

    public const string USERS_UPDATE = 'users.update';

    public const string USERS_DELETE = 'users.delete';

    public const string ROLES_MANAGE = 'roles.manage';

    public const string ACTIVITY_VIEW = 'activity.view';

    public const string SETTINGS_MANAGE = 'settings.manage';

    public const string PLUGINS_MANAGE = 'plugins.manage';

    public const string WORKFLOW_APPROVE = 'workflow.approve';

    public const string SEARCH_REINDEX = 'search.reindex';

    /**
     * Full catalogue with category + description, used by the seeder/registry.
     *
     * @return list<array{name: string, category: string, description: string}>
     */
    public static function catalog(): array
    {
        return [
            ['name' => self::ENTRIES_VIEW, 'category' => 'entries', 'description' => 'View content entries'],
            ['name' => self::ENTRIES_CREATE, 'category' => 'entries', 'description' => 'Create content entries'],
            ['name' => self::ENTRIES_UPDATE, 'category' => 'entries', 'description' => 'Update content entries'],
            ['name' => self::ENTRIES_PUBLISH, 'category' => 'entries', 'description' => 'Publish content entries'],
            ['name' => self::ENTRIES_DELETE, 'category' => 'entries', 'description' => 'Delete content entries'],
            ['name' => self::MEDIA_VIEW, 'category' => 'media', 'description' => 'View media assets'],
            ['name' => self::MEDIA_UPLOAD, 'category' => 'media', 'description' => 'Upload media assets'],
            ['name' => self::MEDIA_DELETE, 'category' => 'media', 'description' => 'Delete media assets'],
            ['name' => self::USERS_VIEW, 'category' => 'users', 'description' => 'View users'],
            ['name' => self::USERS_CREATE, 'category' => 'users', 'description' => 'Create users'],
            ['name' => self::USERS_UPDATE, 'category' => 'users', 'description' => 'Update users'],
            ['name' => self::USERS_DELETE, 'category' => 'users', 'description' => 'Delete users'],
            ['name' => self::ROLES_MANAGE, 'category' => 'roles', 'description' => 'Manage roles and permissions'],
            ['name' => self::ACTIVITY_VIEW, 'category' => 'activity', 'description' => 'View the activity log'],
            ['name' => self::SETTINGS_MANAGE, 'category' => 'settings', 'description' => 'Manage application settings'],
            ['name' => self::PLUGINS_MANAGE, 'category' => 'plugins', 'description' => 'Manage plugins'],
            ['name' => self::WORKFLOW_APPROVE, 'category' => 'workflow', 'description' => 'Approve workflow transitions'],
            ['name' => self::SEARCH_REINDEX, 'category' => 'search', 'description' => 'Trigger search re-indexing'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_map(static fn (array $permission): string => $permission['name'], self::catalog());
    }
}
