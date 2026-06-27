<?php

declare(strict_types=1);

namespace App\Modules\Media\Events;

use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use Illuminate\Foundation\Events\Dispatchable;

final class MediaConverted
{
    use Dispatchable;

    public function __construct(
        public readonly Media $media,
        public readonly MediaConversion $conversion,
    ) {}
}
