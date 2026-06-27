<?php

declare(strict_types=1);

namespace App\Modules\Navigation\DTOs;

/**
 * One item produced by a dynamic NavigationGenerator. Labels and URLs are keyed
 * by locale so the builder can bake a multi-locale node from it once, at build
 * time.
 */
final class GeneratedNavigationItem
{
    /**
     * @param  array<string, string>  $label  locale => title
     * @param  array<string, string|null>  $url  locale => resolved URL
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public readonly array $label,
        public readonly array $url,
        public readonly string $target = '_self',
        public readonly array $meta = [],
    ) {}
}
