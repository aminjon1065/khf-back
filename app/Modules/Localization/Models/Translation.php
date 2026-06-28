<?php

declare(strict_types=1);

namespace App\Modules\Localization\Models;

use Database\Factories\Modules\Localization\TranslationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A single translated string, addressed by group + key + locale. `group` is a
 * namespace (ui/seo/navigation/settings/…); `value` is the localized text and
 * may be null when a key is registered but not yet translated.
 *
 * @property int $id
 * @property string $group
 * @property string $key
 * @property string $locale
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Translation extends Model
{
    /** @use HasFactory<TranslationFactory> */
    use HasFactory;

    protected $table = 'translations';

    protected $fillable = [
        'group',
        'key',
        'locale',
        'value',
    ];

    /**
     * @param  Builder<Translation>  $q
     * @return Builder<Translation>
     */
    public function scopeForLocale(Builder $q, string $locale): Builder
    {
        return $q->where('locale', $locale);
    }

    /**
     * @param  Builder<Translation>  $q
     * @return Builder<Translation>
     */
    public function scopeInGroup(Builder $q, string $group): Builder
    {
        return $q->where('group', $group);
    }

    protected static function newFactory(): TranslationFactory
    {
        return TranslationFactory::new();
    }
}
