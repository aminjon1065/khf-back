<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use App\Models\User;
use Database\Factories\Modules\Identity\ActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Immutable activity-log entry. Any module can publish entries through the
 * ActivityLogger; there is no updated_at (entries are never mutated).
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $type
 * @property string|null $description
 * @property string|null $subject_type
 * @property string|null $subject_id
 * @property array<string, mixed>|null $properties
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 */
class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): ActivityFactory
    {
        return ActivityFactory::new();
    }
}
