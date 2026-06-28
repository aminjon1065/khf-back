<?php

declare(strict_types=1);

namespace App\Modules\Localization\Contracts;

use App\Modules\Localization\Models\LocalizedSlug;
use Illuminate\Support\Collection;

/**
 * Persistence boundary for per-locale slugs. Implementations are Eloquent-backed
 * and are the only place the engine touches the `localized_slugs` table directly.
 */
interface LocalizedSlugRepositoryInterface
{
    public function findBySlug(string $subjectType, string $locale, string $slug): ?LocalizedSlug;

    /**
     * Every slug registered for a subject.
     *
     * @return Collection<int, LocalizedSlug>
     */
    public function forSubject(string $subjectType, string $subjectId): Collection;

    public function slugExists(string $subjectType, string $locale, string $slug, ?int $ignoreId = null): bool;

    public function canonical(string $subjectType, string $subjectId): ?LocalizedSlug;

    public function put(string $subjectType, string $subjectId, string $locale, string $slug, bool $isCanonical = false): LocalizedSlug;
}
