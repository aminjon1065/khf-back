<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * KHF role model — extends Spatie's role with a system flag and description.
 * System roles are part of the engine and may not be deleted or renamed.
 *
 * @property bool $is_system
 * @property string|null $description
 */
class Role extends SpatieRole
{
    /** @var array<string, string> */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function isSystem(): bool
    {
        return (bool) $this->is_system;
    }
}
