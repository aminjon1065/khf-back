<?php

declare(strict_types=1);

namespace App\Core\Models;

use Database\Factories\Core\BlueprintFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $collection_id
 * @property string $name
 */
class Blueprint extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'collection_id',
        'name',
    ];

    /** @return BelongsTo<Collection, $this> */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /** @return HasMany<BlueprintField, $this> */
    public function fields(): HasMany
    {
        return $this->hasMany(BlueprintField::class, 'blueprint_id')->orderBy('order');
    }

    /** @return HasMany<Entry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    protected static function newFactory(): BlueprintFactory
    {
        return BlueprintFactory::new();
    }
}
