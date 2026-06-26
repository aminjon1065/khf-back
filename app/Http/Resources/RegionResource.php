<?php

namespace App\Http\Resources;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Region
 */
class RegionResource extends JsonResource
{
    /**
     * Соответствует Region на фронтенде (khf-front/lib/content/regions.ts).
     * Локаль уже выставлена SetLocaleFromRequest, поэтому переводимые
     * поля отдаём через магические аксессоры spatie/translatable.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'center' => $this->center,
            'risk' => $this->risk->value,
            'activeIncidents' => (int) $this->active_incidents,
            'stations' => (int) $this->stations,
            'note' => $this->note,
        ];
    }
}
