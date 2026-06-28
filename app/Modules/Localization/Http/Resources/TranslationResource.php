<?php

declare(strict_types=1);

namespace App\Modules\Localization\Http\Resources;

use App\Modules\Localization\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Internal HTTP serialization of a single translation row, addressed by
 * group + key + locale.
 *
 * @mixin Translation
 */
final class TranslationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Translation $translation */
        $translation = $this->resource;

        return [
            'group' => $translation->group,
            'key' => $translation->key,
            'locale' => $translation->locale,
            'value' => $translation->value,
            'updated_at' => $translation->updated_at?->toIso8601String(),
        ];
    }
}
