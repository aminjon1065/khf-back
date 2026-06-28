<?php

declare(strict_types=1);

namespace App\Modules\Localization\Http\Resources;

use App\Modules\Localization\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Internal HTTP serialization of a registered locale. Exposes the engine's own
 * columns only, with the text direction flattened to its scalar value.
 *
 * @mixin Locale
 */
final class LocaleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Locale $locale */
        $locale = $this->resource;

        return [
            'code' => $locale->code,
            'name' => $locale->name,
            'native_name' => $locale->native_name,
            'direction' => $locale->direction->value,
            'is_default' => $locale->is_default,
            'is_active' => $locale->is_active,
            'fallback_code' => $locale->fallback_code,
            'alias' => $locale->alias,
            'sort_order' => $locale->sort_order,
            'updated_at' => $locale->updated_at?->toIso8601String(),
        ];
    }
}
