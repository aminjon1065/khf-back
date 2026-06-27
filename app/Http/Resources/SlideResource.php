<?php

namespace App\Http\Resources;

use App\Core\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Entry
 */
class SlideResource extends JsonResource
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
            'id' => $this->id,
            'category' => $locData['category'] ?? '',
            'title' => $locData['title'] ?? '',
            'date' => $global['date'] ?? '',
            'source' => $global['source'] ?? '',
            'newsSlug' => null, // no longer directly mapped via relations unless we load the related entry
            'image' => null, // handled by Media engine in frontend
        ];
    }
}
