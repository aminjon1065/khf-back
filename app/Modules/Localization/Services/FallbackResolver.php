<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\Enums\FallbackStrategy;
use App\Modules\Localization\Support\LocalizationHooks;

/**
 * Builds the ordered, cycle-safe fallback chain for a locale: the requested
 * locale, each `fallback_code` hop, then the default locale. Honours the
 * configured strategy and lets plugins reshape the chain via a filter hook.
 */
final class FallbackResolver
{
    public function __construct(
        private readonly LocaleResolverInterface $locales,
        private readonly HookManagerInterface $hooks,
    ) {}

    /**
     * The ordered, de-duplicated fallback chain for a locale.
     *
     * @return list<string>
     */
    public function chain(string $locale): array
    {
        if (FallbackStrategy::fromConfig() === FallbackStrategy::Strict) {
            return $this->applyHook([$locale], $locale);
        }

        /** @var list<string> $chain */
        $chain = [];
        /** @var array<string, true> $visited */
        $visited = [];

        $maxDepth = max($this->locales->all()->count(), 1);
        $current = $locale;
        $depth = 0;

        while ($current !== null && ! isset($visited[$current]) && $depth <= $maxDepth) {
            $chain[] = $current;
            $visited[$current] = true;
            $current = $this->locales->fallbackFor($current);
            $depth++;
        }

        $default = $this->locales->default();

        if (! isset($visited[$default])) {
            $chain[] = $default;
        }

        return $this->applyHook($chain, $locale);
    }

    /**
     * Pass the chain through the FILTER_FALLBACK_CHAIN hook, keeping it a
     * de-duplicated list of strings.
     *
     * @param  list<string>  $chain
     * @return list<string>
     */
    private function applyHook(array $chain, string $locale): array
    {
        $filtered = $this->hooks->applyFilters(LocalizationHooks::FILTER_FALLBACK_CHAIN, $chain, $locale);

        if (is_array($filtered)) {
            $chain = array_map(strval(...), $filtered);
        }

        return array_values(array_unique($chain));
    }
}
