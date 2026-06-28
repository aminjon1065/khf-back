<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Modules\Localization\Contracts\LocaleResolverInterface;

/**
 * Builds locale-prefixed paths using each locale's public segment (alias). The
 * default locale's prefix can be suppressed via the
 * `khf.localization.prefix_default_locale` config flag.
 */
final class LocalizedUrlGenerator
{
    public function __construct(private readonly LocaleResolverInterface $locales) {}

    public function to(string $path, ?string $locale = null): string
    {
        $locale ??= $this->locales->default();

        $path = '/'.ltrim($path, '/');

        if ($locale === $this->locales->default() && ! $this->prefixDefault()) {
            return $path;
        }

        $segment = $this->locales->publicSegment($locale);

        return '/'.$segment.($path === '/' ? '' : $path);
    }

    /**
     * One localized URL per active locale, keyed by internal locale code.
     *
     * @return array<string, string>
     */
    public function urlsForPath(string $path): array
    {
        /** @var array<string, string> $urls */
        $urls = [];

        foreach ($this->locales->codes() as $code) {
            $urls[$code] = $this->to($path, $code);
        }

        return $urls;
    }

    private function prefixDefault(): bool
    {
        return (bool) config('khf.localization.prefix_default_locale', true);
    }
}
