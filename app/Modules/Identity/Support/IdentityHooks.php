<?php

declare(strict_types=1);

namespace App\Modules\Identity\Support;

/**
 * Extension points exposed by the Identity module, fired through the Core
 * HookManager. Plugins use these to add permissions, roles, and profile fields,
 * or to participate in authentication — without modifying the engine.
 */
final class IdentityHooks
{
    /** @filtered list<string> — add permission names to the canonical catalogue */
    public const string REGISTER_PERMISSIONS = 'khf.identity.permissions';

    /** @filtered array<string, list<string>> — add roles (name => permissions) */
    public const string REGISTER_ROLES = 'khf.identity.roles';

    /** @filtered list<string> — declare additional editable profile field keys */
    public const string PROFILE_FIELDS = 'khf.identity.profile_fields';

    /** Action — fired after a user authenticates (causer passed) */
    public const string AUTHENTICATED = 'khf.identity.authenticated';

    private function __construct() {}
}
