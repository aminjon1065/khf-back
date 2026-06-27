<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->route('locale') ?? app()->getLocale();
        $data = $this->data ?? [];

        // Extract locale-specific data
        $localizedData = $data[$locale] ?? [];

        // Extract global data
        $globalData = $data['global'] ?? [];

        // Flatten into a single array
        $flattened = array_merge($globalData, $localizedData);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            // Spread the flattened dynamic fields
            ...$flattened,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
        ];
    }
}
