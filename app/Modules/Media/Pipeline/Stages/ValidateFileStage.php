<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Modules\Media\Contracts\FileValidatorInterface;
use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Pipeline\UploadContext;
use Closure;

final class ValidateFileStage implements PipelineStageInterface
{
    public function __construct(private readonly FileValidatorInterface $validator) {}

    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        $this->validator->validate($context->file);

        return $next($context);
    }
}
