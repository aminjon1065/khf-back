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
        $locale = $request->route('locale') ?? app()->getLocale();
        $data = $this->data ?? [];
        $localized = $data[$locale] ?? [];
        $global = $data['global'] ?? [];

        return [
            'id' => $global['slug'] ?? null,
            'name' => $localized['name'] ?? null,
            'center' => $localized['center'] ?? null,
            'risk' => $global['risk'] ?? 'low',
            'activeIncidents' => (int) ($global['active_incidents'] ?? 0),
            'stations' => (int) ($global['stations'] ?? 0),
            'note' => $localized['note'] ?? null,
        ];
    }
}
