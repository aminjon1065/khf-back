<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Extended profile for a user. The `meta` bag is the extension point for plugins
 * that add custom profile fields.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $bio
 * @property string|null $phone
 * @property string|null $job_title
 * @property array<string, mixed>|null $meta
 */
class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'phone',
        'job_title',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
