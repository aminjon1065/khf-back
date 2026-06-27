<?php

declare(strict_types=1);

namespace App\Modules\Media\Enums;

enum MediaVisibility: string
{
    case Public = 'public';
    case Private = 'private';

    public function isPublic(): bool
    {
        return $this === self::Public;
    }

    /**
     * Map to the Laravel filesystem visibility constant.
     */
    public function toFilesystemVisibility(): string
    {
        return match ($this) {
            self::Public => 'public',
            self::Private => 'private',
        };
    }
}
