<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Sources;

use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\NavigationItem;

/**
 * Resolves an internal static path (stored in source_value), locale-prefixing it
 * so the frontend receives a locale-aware URL.
 */
final class StaticUrlSourceResolver implements NavigationSourceResolverInterface
{
    public function type(): string
    {
        return NavigationSourceType::StaticUrl->value;
    }

    public function resolve(NavigationItem $item, string $locale): ?string
    {
        $value = $item->source_value;

        if ($value === null || $value === '') {
            return null;
        }

        return '/'.$locale.'/'.ltrim($value, '/');
    }
}
