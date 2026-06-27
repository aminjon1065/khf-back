<?php

declare(strict_types=1);

namespace App\Modules\Media\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class Media extends SpatieMedia
{
    use HasUuids;
    use SoftDeletes;

    /**
     * Get the alt text from custom properties.
     */
    public function getAltAttribute(): ?string
    {
        return $this->getCustomProperty('alt');
    }

    /**
     * Get the focal point from custom properties.
     */
    public function getFocalPointAttribute(): ?array
    {
        return $this->getCustomProperty('focal_point');
    }
}
