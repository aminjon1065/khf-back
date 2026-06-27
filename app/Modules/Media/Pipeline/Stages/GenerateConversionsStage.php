<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Media\Contracts\ImageProcessorInterface;
use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\Enums\ImageFit;
use App\Modules\Media\Pipeline\UploadContext;
use App\Modules\Media\Services\MediaManager;
use App\Modules\Media\Support\MediaHooks;
use Closure;
use Throwable;

/**
 * Generates the default (and responsive) conversions for image uploads.
 * Conversions are best-effort: a failure is reported but never fails the upload.
 */
final class GenerateConversionsStage implements PipelineStageInterface
{
    public function __construct(
        private readonly MediaManager $manager,
        private readonly ImageProcessorInterface $imageProcessor,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        $media = $context->media;

        if ($media === null || ! $context->isImage() || ! $this->imageProcessor->isSupported($context->mimeType)) {
            return $next($context);
        }

        $transformations = $this->resolveTransformations($context);
        $filtered = $this->hooks->applyFilters(MediaHooks::FILTER_CONVERSIONS, $transformations, $media);
        $transformations = is_array($filtered) ? $filtered : $transformations;

        foreach ($transformations as $transformation) {
            if (! $transformation instanceof ImageTransformation) {
                continue;
            }

            try {
                $conversion = $this->manager->generateConversion($media, $context->sourcePath, $transformation);
                $context->generatedConversions[] = $conversion;
                $this->hooks->doAction(MediaHooks::ACTION_CONVERTED, $conversion);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return $next($context);
    }

    /**
     * @return list<ImageTransformation>
     */
    private function resolveTransformations(UploadContext $context): array
    {
        if ($context->requestedConversions !== null) {
            return $context->requestedConversions;
        }

        $transformations = [];

        foreach ((array) config('khf.media.conversions', []) as $definition) {
            if (is_array($definition)) {
                /** @var array{name: string, width?: int|null, height?: int|null, fit?: string, format?: string|null, quality?: int, optimize?: bool} $definition */
                $transformations[] = ImageTransformation::fromArray($definition);
            }
        }

        foreach ((array) config('khf.media.responsive_widths', []) as $width) {
            $transformations[] = new ImageTransformation(
                name: 'responsive-'.(int) $width,
                width: (int) $width,
                fit: ImageFit::Max,
                format: 'webp',
                quality: 82,
            );
        }

        return $transformations;
    }
}
