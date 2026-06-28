<?php

declare(strict_types=1);

namespace App\Modules\Localization\Contracts;

/**
 * Resolves translation values through the configured fallback chain, applying
 * the missing-translation behaviour and firing fallback events when used.
 */
interface TranslationResolverInterface
{
    /**
     * Resolve a single translation, walking the fallback chain and applying the
     * missing-translation behaviour when nothing resolves.
     */
    public function resolve(string $group, string $key, ?string $locale = null): ?string;

    /**
     * Resolve a locale-keyed value map, returning the best match along the
     * fallback chain.
     *
     * @param  array<string, mixed>  $values
     */
    public function resolveValue(array $values, ?string $locale = null, ?string $context = null): mixed;

    /**
     * The ordered, cycle-safe fallback chain for a locale.
     *
     * @return list<string>
     */
    public function chain(string $locale): array;

    /**
     * All resolved translations for a group, keyed by bare key.
     *
     * @return array<string, string>
     */
    public function forLocale(string $group, ?string $locale = null): array;
}
