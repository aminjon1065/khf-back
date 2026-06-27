<?php

declare(strict_types=1);

namespace App\Modules\Media\Exceptions;

final class InvalidMediaFileException extends MediaException
{
    public static function maxSizeExceeded(int $size, int $maxSize): self
    {
        return new self("The file size {$size} bytes exceeds the maximum allowed {$maxSize} bytes.");
    }

    public static function unreadable(): self
    {
        return new self('The uploaded file is missing or could not be read.');
    }
}
