<?php

declare(strict_types=1);

namespace App\Modules\Identity\Contracts;

use App\Models\User;
use App\Modules\Identity\Models\Activity;
use Illuminate\Database\Eloquent\Model;

/**
 * Append-only activity log. Any module records audit-worthy events through this
 * contract; IP and user-agent are captured automatically from the request.
 */
interface ActivityLoggerInterface
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function record(
        string $type,
        ?User $causer = null,
        ?string $description = null,
        array $properties = [],
        ?Model $subject = null,
    ): Activity;
}
