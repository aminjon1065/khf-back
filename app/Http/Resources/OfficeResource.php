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
        return [
            'region' => $this->region,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'hours' => $this->hours,
        ];
    }
}
