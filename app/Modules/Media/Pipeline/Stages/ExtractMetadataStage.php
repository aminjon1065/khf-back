<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Media\Contracts\MetadataExtractorInterface;
use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Pipeline\UploadContext;
use App\Modules\Media\Support\MediaHooks;
use Closure;

final class ExtractMetadataStage implements PipelineStageInterface
{
    public function __construct(
        private readonly MetadataExtractorInterface $extractor,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        $context->metadata = $this->extractor->extract($context->sourcePath, $context->mimeType);

        // Give plugins a chance to enrich/override descriptive metadata.
        $bag = $this->hooks->applyFilters(MediaHooks::FILTER_METADATA, [
            'alt_text' => $context->altText,
            'caption' => $context->caption,
            'copyright' => $context->copyright,
            'custom_properties' => $context->customProperties,
            'width' => $context->metadata->width,
            'height' => $context->metadata->height,
            'dominant_color' => $context->metadata->dominantColor,
            'exif' => $context->metadata->exif,
        ], $context);

        if (is_array($bag)) {
            $context->altText = $this->asString($bag['alt_text'] ?? null) ?? $context->altText;
            $context->caption = $this->asString($bag['caption'] ?? null) ?? $context->caption;
            $context->copyright = $this->asString($bag['copyright'] ?? null) ?? $context->copyright;

            if (isset($bag['custom_properties']) && is_array($bag['custom_properties'])) {
                /** @var array<string, mixed> $custom */
                $custom = $bag['custom_properties'];
                $context->customProperties = $custom;
            }
        }

        return $next($context);
    }

    private function asString(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }
}
