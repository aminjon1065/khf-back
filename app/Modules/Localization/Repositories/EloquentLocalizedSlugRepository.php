<?php

declare(strict_types=1);

namespace App\Modules\Localization\Repositories;

use App\Modules\Localization\Contracts\LocalizedSlugRepositoryInterface;
use App\Modules\Localization\Models\LocalizedSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Eloquent-backed persistence for per-locale slugs. The only place the engine
 * touches the `localized_slugs` table directly.
 */
final class EloquentLocalizedSlugRepository implements LocalizedSlugRepositoryInterface
{
    public function findBySlug(string $subjectType, string $locale, string $slug): ?LocalizedSlug
    {
        return LocalizedSlug::query()
            ->where('subject_type', $subjectType)
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->first();
    }

    /**
     * @return Collection<int, LocalizedSlug>
     */
    public function forSubject(string $subjectType, string $subjectId): Collection
    {
        return LocalizedSlug::query()
            ->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->get();
    }

    public function slugExists(string $subjectType, string $locale, string $slug, ?int $ignoreId = null): bool
    {
        return LocalizedSlug::query()
            ->where('subject_type', $subjectType)
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->when($ignoreId !== null, static fn (Builder $query): Builder => $query->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function canonical(string $subjectType, string $subjectId): ?LocalizedSlug
    {
        return LocalizedSlug::query()
            ->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->canonical()
            ->first();
    }

    public function put(string $subjectType, string $subjectId, string $locale, string $slug, bool $isCanonical = false): LocalizedSlug
    {
        return LocalizedSlug::query()->updateOrCreate(
            [
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'locale' => $locale,
            ],
            [
                'slug' => $slug,
                'is_canonical' => $isCanonical,
            ],
        );
    }
}
