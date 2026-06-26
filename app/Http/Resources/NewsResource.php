<?php

namespace App\Http\Resources;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin News
 */
class NewsResource extends JsonResource
{
    /**
     * Соответствует NewsItem на фронтенде (khf-front/docs/BACKEND.md).
     * Локаль уже выставлена SetLocaleFromRequest, поэтому переводимые
     * поля отдаём через магические аксессоры spatie/translatable.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category?->name,
            'categoryColor' => $this->category?->tone->textClass(),
            'tone' => $this->category?->tone->value,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => $this->body, // переводимый rich-text (HTML)
            'date' => $this->published_at?->format('d.m.Y'),
            'author' => $this->author,
            'region' => $this->region,
            'views' => (int) $this->views,
            'image' => $this->imageUrls(),
        ];
    }

    /**
     * @return array{thumb:string, card:string, hero:string, original:string}|null
     */
    private function imageUrls(): ?array
    {
        $media = $this->getFirstMedia('cover');

        if ($media === null) {
            return null;
        }

        return [
            'thumb' => $media->getUrl('thumb'),
            'card' => $media->getUrl('card'),
            'hero' => $media->getUrl('hero'),
            'original' => $media->getUrl(),
        ];
    }
}
