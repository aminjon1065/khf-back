<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\Contracts\LocalizationCacheInterface;
use App\Modules\Localization\Contracts\TranslationResolverInterface;
use App\Modules\Localization\Enums\MissingTranslation;
use App\Modules\Localization\Events\FallbackUsed;
use App\Modules\Localization\Support\LocalizationHooks;

/**
 * Resolves translation values by walking the fallback chain over the cached
 * per-locale maps. Fires {@see FallbackUsed} when a non-requested locale
 * answers, applies the resolve filter, and honours the missing-value behaviour.
 */
final class TranslationResolver implements TranslationResolverInterface
{
    public function __construct(
        private readonly LocalizationCacheInterface $cache,
        private readonly LocaleResolverInterface $locales,
        private readonly FallbackResolver $fallback,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function resolve(string $group, string $key, ?string $locale = null): ?string
    {
        $requested = $locale ?? $this->locales->current();
        $fullKey = "{$group}.{$key}";

        foreach ($this->fallback->chain($requested) as $step) {
            $map = $this->cache->translations($step);

            if (! array_key_exists($fullKey, $map)) {
                continue;
            }

            $value = $map[$fullKey];

            if ($step !== $requested) {
                $this->events->dispatch(new FallbackUsed($requested, $step, $fullKey));
            }

            $filtered = $this->hooks->applyFilters(LocalizationHooks::FILTER_RESOLVED, $value, $group, $key, $step);

            return is_string($filtered) ? $filtered : $value;
        }

        return $this->missing($fullKey);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function resolveValue(array $values, ?string $locale = null, ?string $context = null): mixed
    {
        $requested = $locale ?? $this->locales->current();
        $label = $context ?? 'value';

        foreach ($this->fallback->chain($requested) as $step) {
            if (! array_key_exists($step, $values)) {
                continue;
            }

            $value = $values[$step];

            if ($value === null || $value === '') {
                continue;
            }

            if ($step !== $requested) {
                $this->events->dispatch(new FallbackUsed($requested, $step, $label));
            }

            return $value;
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public function chain(string $locale): array
    {
        return $this->fallback->chain($locale);
    }

    /**
     * @return array<string, string>
     */
    public function forLocale(string $group, ?string $locale = null): array
    {
        $requested = $locale ?? $this->locales->current();
        $prefix = "{$group}.";
        $length = strlen($prefix);

        /** @var array<string, string> $result */
        $result = [];

        foreach ($this->cache->translations($requested) as $fullKey => $value) {
            if (str_starts_with($fullKey, $prefix)) {
                $result[substr($fullKey, $length)] = $value;
            }
        }

        return $result;
    }

    /**
     * Apply the configured missing-translation behaviour.
     */
    private function missing(string $fullKey): ?string
    {
        return match (MissingTranslation::fromConfig()) {
            MissingTranslation::ReturnNull => null,
            MissingTranslation::ReturnKey => $fullKey,
            MissingTranslation::ReturnEmpty => '',
        };
    }
}
