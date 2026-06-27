<?php

declare(strict_types=1);

namespace App\Core\Models;

use App\Core\Enums\EntryStatus;
use App\Models\User;
use Database\Factories\Core\EntryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $collection_id
 * @property string $blueprint_id
 * @property int|null $author_id
 * @property int|null $updated_by
 * @property EntryStatus $status
 * @property string|null $slug
 * @property array<string, mixed>|null $data
 * @property int $version
 * @property Carbon|null $published_at
 */
class Entry extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'collection_id',
        'blueprint_id',
        'author_id',
        'updated_by',
        'status',
        'slug',
        'data',
        'version',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EntryStatus::class,
            'data' => 'array',
            'version' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Collection, $this> */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /** @return BelongsTo<Blueprint, $this> */
    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** @return BelongsTo<User, $this> */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @param Builder<Entry> $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', EntryStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** @param Builder<Entry> $query */
    public function scopeHasLocale(Builder $query, string $locale): void
    {
        $query->whereNotNull("data->{$locale}");
    }

    protected static function newFactory(): EntryFactory
    {
        return EntryFactory::new();
    }
}
