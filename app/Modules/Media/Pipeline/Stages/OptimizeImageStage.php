<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Modules\Media\Contracts\ImageProcessorInterface;
use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Pipeline\UploadContext;
use Closure;

/**
 * Optimizes the working original in place (best-effort). Runs before checksum
 * and storage so the persisted file and its hash reflect the optimized bytes.
 */
final class OptimizeImageStage implements PipelineStageInterface
{
    public function __construct(private readonly ImageProcessorInterface $imageProcessor) {}

    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        if ($context->isImage() && $this->imageProcessor->isSupported($context->mimeType)) {
            $context->optimizedSize = $this->imageProcessor->optimize($context->sourcePath);
            $context->size = $context->optimizedSize;
        }

        return $next($context);
    }
}
