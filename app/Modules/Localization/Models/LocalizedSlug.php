<?php

declare(strict_types=1);

namespace App\Modules\Localization\Models;

use Database\Factories\Modules\Localization\LocalizedSlugFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * A per-locale URL slug for a polymorphic content subject. `subject_id` is a
 * string to support UUID Entry ids. One slug per subject is marked canonical
 * and used as the resolution fallback when a locale-specific slug is absent.
 *
 * @property int $id
 * @property string $subject_type
 * @property string $subject_id
 * @property string $locale
 * @property string $slug
 * @property bool $is_canonical
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class LocalizedSlug extends Model
{
    /** @use HasFactory<LocalizedSlugFactory> */
    use HasFactory;

    protected $table = 'localized_slugs';

    protected $fillable = [
        'subject_type',
        'subject_id',
        'locale',
        'slug',
        'is_canonical',
    ];

    protected function casts(): array
    {
        return [
            'is_canonical' => 'boolean',
        ];
    }

    /** @return MorphTo<Model, $this> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param  Builder<LocalizedSlug>  $q
     * @return Builder<LocalizedSlug>
     */
    public function scopeForLocale(Builder $q, string $locale): Builder
    {
        return $q->where('locale', $locale);
    }

    /**
     * @param  Builder<LocalizedSlug>  $q
     * @return Builder<LocalizedSlug>
     */
    public function scopeCanonical(Builder $q): Builder
    {
        return $q->where('is_canonical', true);
    }

    protected static function newFactory(): LocalizedSlugFactory
    {
        return LocalizedSlugFactory::new();
    }
}
