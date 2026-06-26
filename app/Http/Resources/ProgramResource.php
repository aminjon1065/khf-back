<?php

namespace App\Http\Resources;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Соответствует ProgramItem на фронтенде (khf-front/lib/content/activity.ts).
 * Локаль уже выставлена SetLocaleFromRequest, поэтому переводимые
 * поля отдаём через магические аксессоры spatie/translatable.
 *
 * @mixin Program
 */
class ProgramResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'period' => $this->period,
            'status' => $this->status->value,
            'description' => $this->description,
        ];
    }
}
