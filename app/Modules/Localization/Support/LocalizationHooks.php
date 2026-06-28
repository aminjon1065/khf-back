<?php

declare(strict_types=1);

namespace App\Modules\Localization\Support;

/**
 * Extension points exposed by the Localization Engine, fired through the Core
 * HookManager. Plugins use these to register locale and translation sources, to
 * alter resolved values or fallback chains, and to react to translation changes
 * — without modifying the engine.
 */
final class LocalizationHooks
{
    /** @filtered list<Locale>|array — register additional locale providers */
    public const string LOCALE_PROVIDERS = 'khf.localization.locale_providers';

    /** @filtered array — extend or register translation sources */
    public const string TRANSLATION_SOURCES = 'khf.localization.translation_sources';

    /** @filtered string — modify a resolved translation value (args: value, group, key, locale) */
    public const string FILTER_RESOLVED = 'khf.localization.resolve';

    /** @filtered list<string> — modify the fallback chain (args: chain, locale) */
    public const string FILTER_FALLBACK_CHAIN = 'khf.localization.fallback_chain';

    /** @filtered array<string, mixed> — modify locale validation rules (args: LocaleData) */
    public const string FILTER_VALIDATION_RULES = 'khf.localization.validation_rules';

    /** Action — fired in boot with the LocalizationService */
    public const string REGISTER = 'khf.localization.register';

    /** Action — fired after a translation value changes (args: group, key, locale, value) */
    public const string TRANSLATION_CHANGED = 'khf.localization.translation_changed';

    private function __construct() {}
}
