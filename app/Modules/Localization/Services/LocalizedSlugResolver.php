<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\Contracts\LocalizedSlugRepositoryInterface;
use App\Modules\Localization\Contracts\TranslationResolverInterface;
use App\Modules\Localization\Models\LocalizedSlug;
use Illuminate\Support\Str;

/**
 * Resolves the per-locale slug for a subject, walking the fallback chain and
 * falling back to the canonical slug. Also mints collision-free slugs by
 * appending a numeric suffix until the candidate is free for the locale.
 */
final class LocalizedSlugResolver
{
    public function __construct(
        private readonly LocalizedSlugRepositoryInterface $slugs,
        private readonly LocaleResolverInterface $locales,
        private readonly TranslationResolverInterface $translations,
    ) {}

    public function resolve(string $subjectType, string $subjectId, ?string $locale = null): ?string
    {
        $requested = $locale ?? $this->locales->current();

        foreach ($this->translations->chain($requested) as $step) {
            $row = $this->slugs->forSubject($subjectType, $subjectId)
                ->first(static fn (LocalizedSlug $slug): bool => $slug->locale === $step);

            if ($row !== null) {
                return $row->slug;
            }
        }

        return $this->slugs->canonical($subjectType, $subjectId)?->slug;
    }

    /**
     * Produce a slug that is unique for the subject type within the locale,
     * appending `-2`, `-3`, ... until a free candidate is found.
     */
    public function unique(string $subjectType, string $locale, string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);

        if (! $this->slugs->slugExists($subjectType, $locale, $slug, $ignoreId)) {
            return $slug;
        }

        $suffix = 2;

        while ($this->slugs->slugExists($subjectType, $locale, "{$slug}-{$suffix}", $ignoreId)) {
            $suffix++;
        }

        return "{$slug}-{$suffix}";
    }
}
