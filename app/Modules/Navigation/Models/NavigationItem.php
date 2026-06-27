<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Models;

use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationVisibility;
use Database\Factories\Modules\Navigation\NavigationItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A single node in a navigation tree. Self-references via parent_id for
 * unlimited-depth nesting; ordered within its parent by `order`.
 *
 * @property string $id
 * @property string $navigation_id
 * @property string|null $parent_id
 * @property int $order
 * @property array<string, string> $label
 * @property NavigationSourceType|null $source_type
 * @property string|null $source_id
 * @property string|null $source_value
 * @property string $target
 * @property NavigationVisibility $visibility
 * @property list<string>|null $visibility_rules
 * @property string|null $generator
 * @property array<string, mixed>|null $meta
 * @property bool $is_active
 */
class NavigationItem extends Model
{
    /** @use HasFactory<NavigationItemFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'navigation_id',
        'parent_id',
        'order',
        'label',
        'source_type',
        'source_id',
        'source_value',
        'target',
        'visibility',
        'visibility_rules',
        'generator',
        'meta',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'label' => 'array',
            'source_type' => NavigationSourceType::class,
            'visibility' => NavigationVisibility::class,
            'visibility_rules' => 'array',
            'meta' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Cascade a soft delete to descendants. The DB foreign-key cascade only
        // fires on a hard delete, so without this a soft-deleted parent would
        // leave its children as live but unreachable orphans.
        static::deleting(function (NavigationItem $item): void {
            if ($item->isForceDeleting()) {
                return;
            }

            foreach ($item->children()->get() as $child) {
                $child->delete();
            }
        });
    }

    /** @return BelongsTo<Navigation, $this> */
    public function navigation(): BelongsTo
    {
        return $this->belongsTo(Navigation::class);
    }

    /** @return BelongsTo<NavigationItem, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<NavigationItem, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    /** @param Builder<NavigationItem> $query */
    public function scopeRoots(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    /** @param Builder<NavigationItem> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected static function newFactory(): NavigationItemFactory
    {
        return NavigationItemFactory::new();
    }
}
