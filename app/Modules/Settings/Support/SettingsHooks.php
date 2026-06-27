<?php

declare(strict_types=1);

namespace App\Modules\Settings\Support;

/**
 * Extension points exposed by the Settings Engine, fired through the Core
 * HookManager. Plugins use these to register new setting types, validators and
 * groups, or to react to setting changes — without modifying the engine.
 */
final class SettingsHooks
{
    /** Action — register types/groups/validators during boot (the registries are passed) */
    public const string REGISTER = 'khf.settings.register';

    /** @filtered list<string> — append/alter the validation rules for a setting before persistence */
    public const string FILTER_RULES = 'khf.settings.rules';

    /** Action — fired after a setting value changes (key, value, previous) */
    public const string CHANGED = 'khf.settings.changed';

    private function __construct() {}
}
