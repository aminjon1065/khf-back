<?php

namespace App\Http\Resources;

use App\Core\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Entry
 */
class DocumentResource extends JsonResource
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

        return [
            'id' => $this->slug,
            'slug' => $this->slug,
            'title' => $locData['title'] ?? '',
            'category' => $categorySlug,
            'number' => $global['number'] ?? '',
            'date' => $global['document_date'] ?? null,
            'type' => $global['type'] ?? 'PDF',
            'size' => $global['size'] ?? '',
            'file' => null, // Media logic here
        ];
    }
}
