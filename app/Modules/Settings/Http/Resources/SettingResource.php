<?php

declare(strict_types=1);

namespace App\Modules\Settings\Http\Resources;

use App\Modules\Settings\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Internal HTTP serialization of a persisted setting override. Exposes the
 * engine's own columns only — no storage internals leak through.
 *
 * @mixin Setting
 */
final class SettingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Setting $setting */
        $setting = $this->resource;

        return [
            'key' => $setting->key,
            'group' => $setting->group,
            'type' => $setting->type,
            'value' => $setting->value,
            'updated_at' => $setting->updated_at?->toIso8601String(),
        ];
    }
}
