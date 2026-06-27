<?php

declare(strict_types=1);

namespace App\Modules\Identity\Http\Resources;

use App\Modules\Identity\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Permission
 */
final class PermissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Permission $permission */
        $permission = $this->resource;

        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'description' => $permission->description,
            'category' => $permission->category,
        ];
    }
}
