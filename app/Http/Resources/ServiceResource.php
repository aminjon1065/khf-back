<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
{
    /**
     * Соответствует Service на фронтенде (khf-front/lib/data.ts).
     * Локаль уже выставлена middleware, поэтому переводимые
     * поля отдаём через магические аксессоры spatie/translatable.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->key,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'primary' => $this->is_primary,
            'tel' => $this->tel,
            'route' => $this->route_key,
            'icon' => $this->icon,
        ];
    }
}
