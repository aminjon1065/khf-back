<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Services;

/**
 * A per-build identity map shared by the source resolvers so an Entry/Collection
 * referenced by several items (or resolved across several locales) is loaded
 * once, not once per item per locale. The builder resets it at the start of each
 * build so a rebuilt tree always reflects fresh data.
 */
final class NavigationSourceCache
{
    /** @var array<string, mixed> */
    private array $store = [];

    public function reset(): void
    {
        $this->store = [];
    }

    /**
     * @template TValue
     *
     * @param  callable(): TValue  $loader
     * @return TValue
     */
    public function remember(string $key, callable $loader): mixed
    {
        if (! array_key_exists($key, $this->store)) {
            $this->store[$key] = $loader();
        }

        return $this->store[$key];
    }
}
