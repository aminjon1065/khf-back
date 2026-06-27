<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Enums;

/**
 * Where a navigation item points. Each native source has a resolver registered
 * in the NavigationSourceRegistry; plugins add further sources via the registry.
 */
enum NavigationSourceType: string
{
    case Entry = 'entry';
    case Collection = 'collection';
    case StaticUrl = 'static_url';
    case ExternalUrl = 'external_url';
    case Route = 'route';
    case ModulePage = 'module_page';
    case PluginPage = 'plugin_page';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $type): string => $type->value, self::cases());
    }
}
