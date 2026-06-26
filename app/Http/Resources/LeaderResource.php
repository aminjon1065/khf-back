<?php

namespace App\Http\Resources;

use App\Models\Leader;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Leader
 */
class LeaderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'role' => $this->role,
            'rank' => $this->rank,
            'bio' => $this->bio,
            'photo' => $this->imageUrls(),
        ];
    }

    /**
     * @return array{thumb:string, card:string, hero:string, original:string}|null
     */
    private function imageUrls(): ?array
    {
        $media = $this->getFirstMedia('photo');

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
