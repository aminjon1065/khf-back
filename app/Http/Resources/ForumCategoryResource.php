<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Entry
 */
class ForumCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $data = $this->data ?? [];
        $global = $data['global'] ?? [];
        $locData = $data[$locale] ?? $data['tg'] ?? [];

        return [
            'id' => $this->slug,
            'slug' => $this->slug,
            'icon' => $global['icon'] ?? '',
            'title' => $locData['title'] ?? '',
            'description' => $locData['description'] ?? '',
            'topics_count' => Entry::whereHas('collection', fn ($q) => $q->where('slug', 'forum-topics'))->where('data->global->category_id', $this->id)->count(),
            'posts_count' => 0, // Simplified for now
        ];
    }
}
