<?php

namespace App\Http\Resources;

use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForumCategory
 */
class ForumCategoryResource extends JsonResource
{
    /**
     * Соответствует ForumCategory на фронтенде
     * (khf-front/lib/content/forum.ts). Локаль уже выставлена
     * SetLocaleFromRequest, переводимые поля отдаём через
     * магические аксессоры spatie/translatable.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'topics' => (int) $this->topics_count,
            'posts' => (int) $this->posts_count,
            'icon' => $this->icon,
        ];
    }
}
