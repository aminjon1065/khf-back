<?php

declare(strict_types=1);

namespace App\Modules\Media\Exceptions;

final class MediaNotFoundException extends MediaException
{
    public static function withId(string $id): self
    {
        return new self("No media asset found with id [{$id}].");
    }
}
