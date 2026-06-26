<?php

namespace App\Http\Resources;

use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Соответствует Direction на фронтенде (khf-front/lib/content/activity.ts).
 * Локаль уже выставлена SetLocaleFromRequest, поэтому переводимые
 * поля отдаём через магические аксессоры spatie/translatable.
 *
 * @mixin Direction
 */
class DirectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->key,
            'icon' => $this->icon,
            'title' => $this->title,
            'description' => $this->description,
            'stat' => [
                'value' => $this->stat_value,
                'label' => $this->stat_label,
            ],
        ];
    }
}
