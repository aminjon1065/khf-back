<?php

declare(strict_types=1);

namespace App\Modules\Media\DTOs;

use App\Modules\Media\Enums\ImageFit;

/**
 * Declarative description of one image conversion to generate. Immutable; the
 * imaging adapter consumes it to produce a derivative file.
 */
final class ImageTransformation
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ImageFit $fit = ImageFit::Contain,
        public readonly ?string $format = null,
        public readonly int $quality = 85,
        public readonly bool $optimize = true,
        public readonly ?int $rotation = null,
    ) {}

    /**
     * @param  array{name: string, width?: int|null, height?: int|null, fit?: string, format?: string|null, quality?: int, optimize?: bool, rotation?: int|null}  $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            name: $config['name'],
            width: $config['width'] ?? null,
            height: $config['height'] ?? null,
            fit: isset($config['fit']) ? ImageFit::from($config['fit']) : ImageFit::Contain,
            format: $config['format'] ?? null,
            quality: $config['quality'] ?? 85,
            optimize: $config['optimize'] ?? true,
            rotation: $config['rotation'] ?? null,
        );
    }
}
