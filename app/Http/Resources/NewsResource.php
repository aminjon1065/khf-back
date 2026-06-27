<?php

namespace App\Http\Resources;

use App\Core\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Entry
 */
class NewsResource extends JsonResource
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
        $categoryName = null;
        if ($categoryId) {
            $catEntry = Entry::find($categoryId);
            if ($catEntry) {
                $catData = $catEntry->data ?? [];
                $categoryName = ($catData[$locale]['title'] ?? $catData['tg']['title'] ?? '');
            }
        }

        $date = $this->published_at ? Carbon::parse($this->published_at)->format('d.m.Y') : '';

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $categoryName,
            'categoryColor' => 'text-primary-600', // default fallback
            'tone' => 'brand', // default fallback
            'title' => $locData['title'] ?? '',
            'excerpt' => $locData['excerpt'] ?? '',
            'body' => $locData['body'] ?? '',
            'date' => $date,
            'author' => $global['author'] ?? '',
            'region' => $global['region'] ?? '',
            'views' => (int) ($global['views'] ?? 0),
            'image' => null, // handled by media engine on frontend
        ];
    }
}
