<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Http\Resources;

use App\Modules\Navigation\DTOs\NavigationNode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes a resolved navigation node (and, recursively, its children) into the
 * shape the Next.js frontend consumes. Label and URL are already resolved for the
 * request locale.
 *
 * @mixin NavigationNode
 */
final class NavigationItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var NavigationNode $node */
        $node = $this->resource;

        return [
            'id' => $node->id,
            'label' => $node->label,
            'url' => $node->url,
            'target' => $node->target,
            'type' => $node->sourceType,
            'active' => $node->active,
            'meta' => $node->meta,
            'children' => self::collection($node->children),
        ];
    }
}
