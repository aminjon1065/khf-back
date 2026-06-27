<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Modules\Media\Contracts\ChecksumGeneratorInterface;
use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Pipeline\UploadContext;
use Closure;

final class GenerateChecksumStage implements PipelineStageInterface
{
    public function __construct(private readonly ChecksumGeneratorInterface $checksum) {}

    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        $context->checksum = $this->checksum->hash($context->sourcePath);

        return $next($context);
    }
}
