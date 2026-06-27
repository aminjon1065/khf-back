<?php

declare(strict_types=1);

namespace App\Modules\Identity\Http\Resources;

use App\Modules\Identity\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
final class ActivityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Activity $activity */
        $activity = $this->resource;

        return [
            'id' => $activity->id,
            'type' => $activity->type,
            'description' => $activity->description,
            'user_id' => $activity->user_id,
            'subject_type' => $activity->subject_type,
            'subject_id' => $activity->subject_id,
            'properties' => $activity->properties,
            'ip_address' => $activity->ip_address,
            'created_at' => $activity->created_at?->toIso8601String(),
        ];
    }
}
