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
        return [
            'number' => $this->number,
            'label' => $this->label,
            'note' => $this->note,
            'primary' => $this->is_primary,
        ];
    }
}
