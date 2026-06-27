<?php

namespace App\Http\Resources;

use App\Core\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Entry
 */
class ServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $data = $this->data ?? [];
        $global = $data['global'] ?? [];
        $locData = $data[$locale] ?? $data['tg'] ?? []; // Fallback to 'tg'

        return [
            'id' => $global['key'] ?? '',
            'title' => $locData['title'] ?? '',
            'subtitle' => $locData['subtitle'] ?? '',
            'primary' => (bool) ($global['is_primary'] ?? false),
            'tel' => $global['tel'] ?? '',
            'route' => $global['route_key'] ?? '',
            'icon' => $global['icon'] ?? '',
        ];
    }
}
