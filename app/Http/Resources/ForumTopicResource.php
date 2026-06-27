<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Entry
 */
class ForumTopicResource extends JsonResource
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

        $categoryId = $global['category_id'] ?? null;
        $categorySlug = null;
        if ($categoryId) {
            $catEntry = Entry::find($categoryId);
            if ($catEntry) {
                $categorySlug = $catEntry->slug;
            }
        }

        $date = $global['last_activity'] ? Carbon::parse($global['last_activity'])->format('d.m.Y H:i') : '';

        return [
            'id' => $this->slug ?? $this->id,
            'category' => $categorySlug,
            'title' => $locData['title'] ?? '',
            'author' => $global['author'] ?? '',
            'replies' => (int) ($global['replies'] ?? 0),
            'views' => (int) ($global['views'] ?? 0),
            'pinned' => (bool) ($global['pinned'] ?? false),
            'lastActivity' => $date,
        ];
    }
}
