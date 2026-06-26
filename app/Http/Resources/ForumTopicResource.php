<?php

namespace App\Http\Resources;

use App\Models\ForumTopic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForumTopic
 */
class ForumTopicResource extends JsonResource
{
    /**
     * Соответствует ForumTopic на фронтенде
     * (khf-front/lib/content/forum.ts). Локаль уже выставлена
     * SetLocaleFromRequest, переводимые поля отдаём через
     * магические аксессоры spatie/translatable.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category?->slug,
            'author' => $this->author,
            'replies' => (int) $this->replies,
            'views' => (int) $this->views,
            'lastActivity' => $this->last_activity,
            'pinned' => (bool) $this->pinned,
        ];
    }
}
