<?php

declare(strict_types=1);

namespace App\Core\Models;

use Database\Factories\Core\CollectionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 */
class Collection extends Model
{
    /** @use HasFactory<CollectionFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    /** @return HasMany<Blueprint, $this> */
    public function blueprints(): HasMany
    {
        return $this->hasMany(Blueprint::class);
    }

    /** @return HasMany<Entry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    protected static function newFactory(): CollectionFactory
    {
        return CollectionFactory::new();
    }
}
