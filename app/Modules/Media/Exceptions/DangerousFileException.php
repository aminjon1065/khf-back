<?php

declare(strict_types=1);

namespace App\Modules\Media\Exceptions;

final class DangerousFileException extends MediaException
{
    public static function dangerousExtension(string $segment): self
    {
        return new self("The file name contains a disallowed executable segment [{$segment}].");
    }

    public static function dangerousMimeType(string $mimeType): self
    {
        return new self("The file's content type [{$mimeType}] is rejected as a potential XSS/executable vector.");
    }

    public static function virusDetected(string $signature): self
    {
        return new self("The file was rejected by the virus scanner: {$signature}.");
    }
}
