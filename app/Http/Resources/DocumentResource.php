<?php

namespace App\Http\Resources;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Document
 */
class DocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->slug,
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category?->slug,
            'number' => $this->number,
            'date' => $this->document_date?->format('d.m.Y'),
            'type' => $this->type->value,
            'size' => $this->size,
            'file' => $this->getFirstMediaUrl('file') ?: null,
        ];
    }
}
