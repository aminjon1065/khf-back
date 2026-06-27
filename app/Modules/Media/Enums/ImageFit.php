<?php

declare(strict_types=1);

namespace App\Modules\Media\Enums;

/**
 * Module-owned fit strategy for image conversions. The imaging adapter maps
 * these to the underlying library's equivalents, so Spatie's own enum never
 * leaks outside the Imaging namespace.
 */
enum ImageFit: string
{
    case Contain = 'contain';
    case Crop = 'crop';
    case Fill = 'fill';
    case Max = 'max';
    case Stretch = 'stretch';
}
