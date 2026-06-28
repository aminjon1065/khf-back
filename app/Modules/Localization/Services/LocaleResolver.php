<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\Contracts\LocalizationCacheInterface;
use App\Modules\Localization\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Resolves the supported locale set, their fallbacks, and the public/internal
 * alias mapping. Synthesises a transient registry from config when the table is
 * empty so the engine works before seeding and in fresh test databases.
 */
final class LocaleResolver implements LocaleResolverInterface
{
    public function __construct(private readonly LocalizationCacheInterface $cache) {}

    /**
     * Every known locale, falling back to a config-synthesised registry when the
     * `locales` table is empty.
     *
     * @return Collection<int, Locale>
     */
    public function all(): Collection
    {
        $locales = $this->cache->locales();

        if ($locales->isNotEmpty()) {
            return $locales;
        }

        return $this->synthesize();
    }

    /**
     * @return list<string>
     */
    public function codes(): array
    {
        return array_values(
            $this->all()
                ->filter(static fn (Locale $locale): bool => $locale->is_active)
                ->map(static fn (Locale $locale): string => $locale->code)
                ->all()
        );
    }

    public function default(): string
    {
        $default = $this->all()->first(static fn (Locale $locale): bool => $locale->is_default);

        if ($default instanceof Locale) {
            return $default->code;
        }

        $configured = config('khf.default_locale', 'tg');

        return is_string($configured) && $configured !== '' ? $configured : 'tg';
    }

    public function fallbackFor(string $locale): ?string
    {
        $model = $this->all()->first(static fn (Locale $candidate): bool => $candidate->code === $locale);

        return $model instanceof Locale ? $model->fallback_code : null;
    }

    public function isSupported(string $locale): bool
    {
        return in_array($locale, $this->codes(), true);
    }

    public function normalize(string $candidate): ?string
    {
        if ($candidate === '') {
            return null;
        }

        $aliasMap = $this->aliasMap();

        if (isset($aliasMap[$candidate])) {
            return $aliasMap[$candidate];
        }

        return $this->isSupported($candidate) ? $candidate : null;
    }

    public function publicSegment(string $code): string
    {
        $model = $this->all()->first(static fn (Locale $locale): bool => $locale->code === $code);

        if ($model instanceof Locale && $model->alias !== null && $model->alias !== '') {
            return $model->alias;
        }

        return $code;
    }

    public function resolveFromRequest(Request $request): string
    {
        $segment = $request->route('locale');

        if (is_string($segment) && $segment !== '') {
            $normalized = $this->normalize($segment);

            if ($normalized !== null) {
                return $normalized;
            }
        }

        $preferred = $request->getPreferredLanguage($this->codes());

        if (is_string($preferred)) {
            $normalized = $this->normalize($preferred);

            if ($normalized !== null) {
                return $normalized;
            }
        }

        return $this->default();
    }

    public function current(): string
    {
        return (string) app()->getLocale();
    }

    /**
     * Build the public-alias → internal-code lookup from the registry.
     *
     * @return array<string, string>
     */
    private function aliasMap(): array
    {
        /** @var array<string, string> $map */
        $map = [];

        foreach ($this->all() as $locale) {
            if ($locale->alias !== null && $locale->alias !== '') {
                $map[$locale->alias] = $locale->code;
            }
        }

        return $map;
    }

    /**
     * Build transient (un-persisted) Locale models from config so the engine has
     * a working registry before the `locales` table is seeded.
     *
     * @return Collection<int, Locale>
     */
    private function synthesize(): Collection
    {
        $configuredDefault = config('khf.default_locale', 'tg');
        $default = is_string($configuredDefault) && $configuredDefault !== '' ? $configuredDefault : 'tg';

        $configuredCodes = config('khf.locales', [$default]);
        /** @var list<string> $codes */
        $codes = is_array($configuredCodes) && $configuredCodes !== []
            ? array_values(array_map(strval(...), $configuredCodes))
            : [$default];

        $sortOrder = 0;

        /** @var Collection<int, Locale> $locales */
        $locales = collect($codes)->map(function (string $code) use ($default, &$sortOrder): Locale {
            $isDefault = $code === $default;
            $sortOrder++;

            return new Locale([
                'code' => $code,
                'name' => $code,
                'native_name' => $code,
                'direction' => 'ltr',
                'is_default' => $isDefault,
                'is_active' => true,
                'fallback_code' => $isDefault ? null : $default,
                'alias' => null,
                'sort_order' => $sortOrder,
            ]);
        })->values();

        return $locales;
    }
}
