<?php

namespace App\Http\Resources;

use App\Models\RegionalOffice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RegionalOffice
 */
class RegionalOfficeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'region' => $this->region,
            'head' => $this->head,
            'phone' => $this->phone,
            'address' => $this->address,
        ];
    }
}
