<?php

namespace App\Http\Resources;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Office
 */
class OfficeResource extends JsonResource
{
    /**
     * Соответствует Office на фронтенде (khf-front/lib/content/contacts.ts).
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
            'region' => $localized['region'] ?? null,
            'address' => $localized['address'] ?? null,
            'phone' => $global['phone'] ?? null,
            'email' => $global['email'] ?? null,
            'hours' => $localized['hours'] ?? null,
        ];
    }
}
