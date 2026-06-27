<?php

declare(strict_types=1);

namespace App\Modules\Media\Events;

use App\Modules\Media\Models\Media;
use Illuminate\Foundation\Events\Dispatchable;

final class MediaUploaded
{
    use Dispatchable;

    public function __construct(public readonly Media $media) {}
}
