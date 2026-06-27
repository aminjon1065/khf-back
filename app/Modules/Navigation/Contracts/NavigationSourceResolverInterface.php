<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Modules\Navigation\Models\NavigationItem;

/**
 * Resolves a navigation item of a given source type to a URL for one locale.
 * One implementation per source type; further sources plug in via the registry.
 */
interface NavigationSourceResolverInterface
{
    /**
     * The source type this resolver handles (a NavigationSourceType value or a
     * custom plugin source name).
     */
    public function type(): string;

    /**
     * The resolved URL for the item in the given locale, or null when the target
     * is unavailable for that locale (e.g. an unpublished or untranslated entry).
     */
    public function resolve(NavigationItem $item, string $locale): ?string;
}
