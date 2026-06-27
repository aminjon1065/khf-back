<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Models;

use App\Modules\Navigation\Enums\NavigationType;
use Database\Factories\Modules\Navigation\NavigationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A navigation menu container (header, footer, sidebar, …). Its items form an
 * unlimited-depth tree.
 *
 * The `type` is stored as a plain string so plugin-registered (non-native) types
 * are supported; the NavigationType enum is just the native catalogue.
 *
 * @property string $id
 * @property string $handle
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property bool $is_active
 * @property array<string, mixed>|null $settings
 */
class Navigation extends Model
{
    /** @use HasFactory<NavigationFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'handle',
        'name',
        'type',
        'description',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    /**
     * Normalize an assigned NavigationType enum or string down to its string value.
     *
     * @return Attribute<string, string>
     */
    protected function type(): Attribute
    {
        return Attribute::make(
            set: fn (NavigationType|string $value): string => $value instanceof NavigationType ? $value->value : $value,
        );
    }

    /** @return HasMany<NavigationItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->orderBy('order');
    }

    /** @return HasMany<NavigationItem, $this> */
    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }

    /** @return HasMany<NavigationItem, $this> */
    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id');
    }

    /** @param Builder<Navigation> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected static function newFactory(): NavigationFactory
    {
        return NavigationFactory::new();
    }
}
