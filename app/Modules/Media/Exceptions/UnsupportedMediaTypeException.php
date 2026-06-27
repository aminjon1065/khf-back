<?php

declare(strict_types=1);

namespace App\Modules\Media\Exceptions;

final class UnsupportedMediaTypeException extends MediaException
{
    public static function mime(string $mimeType): self
    {
        return new self("The MIME type [{$mimeType}] is not allowed.");
    }
}
