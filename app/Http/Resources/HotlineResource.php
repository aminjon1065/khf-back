<?php

namespace App\Http\Resources;

use App\Models\Hotline;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Hotline
 */
class HotlineResource extends JsonResource
{
    /**
     * Соответствует Hotline на фронтенде (khf-front/lib/content/contacts.ts).
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
            'number' => $global['number'] ?? null,
            'label' => $localized['label'] ?? null,
            'note' => $localized['note'] ?? null,
            'primary' => $global['is_primary'] ?? false,
        ];
    }
}
