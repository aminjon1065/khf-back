<?php

namespace App\Http\Resources;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DocumentCategory
 */
class DocumentCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->slug,
            'slug' => $this->slug,
            'label' => $this->name,
            'count' => (int) ($this->documents_count ?? $this->documents()->count()),
        ];
    }
}
