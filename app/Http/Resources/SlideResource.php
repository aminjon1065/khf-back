<?php

namespace App\Http\Resources;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Slide
 */
class SlideResource extends JsonResource
{
    /**
     * Соответствует Slide на фронтенде (khf-front/lib/data.ts).
     * Локаль уже выставлена middleware, поэтому переводимые
     * поля отдаём через магические аксессоры spatie/translatable.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'title' => $this->title,
            'date' => $this->date,
            'source' => $this->source,
            'newsSlug' => $this->news?->slug,
            'image' => $this->imageUrls(),
        ];
    }

    /**
     * @return array{thumb:string, card:string, hero:string, original:string}|null
     */
    private function imageUrls(): ?array
    {
        $media = $this->getFirstMedia('image');

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
