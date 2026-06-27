<?php

declare(strict_types=1);

namespace App\Modules\Media\DTOs;

use App\Modules\Media\Enums\MediaVisibility;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Input for a media upload. Carries the source file plus storage intent and any
 * descriptive metadata supplied at upload time.
 */
final class UploadMediaData
{
    /**
     * @param  list<ImageTransformation>|null  $conversions  null = use the engine defaults
     * @param  array<string, mixed>  $customProperties
     */
    public function __construct(
        public readonly File $file,
        public readonly ?string $disk = null,
        public readonly ?string $driver = null,
        public readonly MediaVisibility $visibility = MediaVisibility::Public,
        public readonly ?string $name = null,
        public readonly ?string $altText = null,
        public readonly ?string $caption = null,
        public readonly ?string $copyright = null,
        public readonly array $customProperties = [],
        public readonly ?int $uploadedBy = null,
        public readonly ?array $conversions = null,
    ) {}
}
