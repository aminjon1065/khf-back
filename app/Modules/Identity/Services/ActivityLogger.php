<?php

declare(strict_types=1);

namespace App\Modules\Identity\Services;

use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Models\Activity;
use Illuminate\Database\Eloquent\Model;

final class ActivityLogger implements ActivityLoggerInterface
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
    ): Activity {
        $request = request();

        return Activity::create([
            'user_id' => $causer?->id,
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject !== null ? (string) $subject->getKey() : null,
            'properties' => $properties === [] ? null : $properties,
            'ip_address' => $request->ip(),
            'user_agent' => $this->truncate($request->userAgent()),
            'created_at' => now(),
        ]);
    }

    private function truncate(?string $value): ?string
    {
        return $value !== null ? mb_substr($value, 0, 1000) : null;
    }
}
