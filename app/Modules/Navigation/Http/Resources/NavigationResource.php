<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Http\Resources;

use App\Modules\Navigation\DTOs\NavigationTree;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes a resolved navigation menu (a NavigationTree) for the Next.js
 * frontend: the menu identity plus its localized, visibility-filtered item tree.
 *
 * @mixin NavigationTree
 */
final class NavigationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var NavigationTree $tree */
        $tree = $this->resource;

        return [
            'handle' => $tree->handle,
            'type' => $tree->type,
            'locale' => $tree->locale,
            'items' => NavigationItemResource::collection($tree->items),
        ];
    }
}
