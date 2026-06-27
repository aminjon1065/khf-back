<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Sources;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Support\NavigationHooks;

/**
 * Resolves a module-registered page. The page key lives in source_value; modules
 * map it to a localized URL through the MODULE_PAGE_URL filter — the extension
 * point for module-owned navigation targets.
 */
final class ModulePageSourceResolver implements NavigationSourceResolverInterface
{
    public function __construct(private readonly HookManagerInterface $hooks) {}

    public function type(): string
    {
        return NavigationSourceType::ModulePage->value;
    }

    public function resolve(NavigationItem $item, string $locale): ?string
    {
        $key = $item->source_value;

        if ($key === null || $key === '') {
            return null;
        }

        $url = $this->hooks->applyFilters(NavigationHooks::MODULE_PAGE_URL, null, $key, $locale);

        return is_string($url) && $url !== '' ? $url : null;
    }
}
