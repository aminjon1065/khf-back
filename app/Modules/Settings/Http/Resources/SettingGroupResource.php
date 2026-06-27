<?php

declare(strict_types=1);

namespace App\Modules\Settings\Http\Resources;

use App\Modules\Settings\DTOs\SettingGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Internal HTTP serialization of a registered setting group.
 *
 * @mixin SettingGroup
 */
final class SettingGroupResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var SettingGroup $group */
        $group = $this->resource;

        return [
            'name' => $group->name,
            'label' => $group->label,
            'description' => $group->description,
        ];
    }
}
