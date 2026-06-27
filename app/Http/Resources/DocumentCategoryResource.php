<?php

namespace App\Http\Resources;

use App\Core\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Entry
 */
class DocumentCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $data = $this->data ?? [];
        $locData = $data[$locale] ?? $data['tg'] ?? [];

        return [
            'id' => $this->slug,
            'title' => $locData['title'] ?? '',
        ];
    }
}
