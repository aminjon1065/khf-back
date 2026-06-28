<?php

declare(strict_types=1);

namespace App\Modules\Localization\Models;

use App\Modules\Localization\Enums\TextDirection;
use Database\Factories\Modules\Localization\LocaleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A supported language in the registry. `code` is the internal ISO code used by
 * the engine; `alias` is the optional public-facing URL segment. `fallback_code`
 * is a soft reference (no FK) to another locale used when a value is missing.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $native_name
 * @property TextDirection $direction
 * @property bool $is_default
 * @property bool $is_active
 * @property string|null $fallback_code
 * @property string|null $alias
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Locale extends Model
{
    /** @use HasFactory<LocaleFactory> */
    use HasFactory;

    protected $table = 'locales';

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
        'is_default',
        'is_active',
        'fallback_code',
        'alias',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'direction' => TextDirection::class,
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Locale>  $q
     * @return Builder<Locale>
     */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /**
     * @param  Builder<Locale>  $q
     * @return Builder<Locale>
     */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('code');
    }

    protected static function newFactory(): LocaleFactory
    {
        return LocaleFactory::new();
    }
}
