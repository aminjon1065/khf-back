<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Pipeline\UploadContext;
use Closure;

/**
 * One step of the upload pipeline. Stages mutate the shared UploadContext and
 * call $next to continue. New stages can be inserted to extend the pipeline.
 */
interface PipelineStageInterface
{
    /**
     * @param  Closure(UploadContext): UploadContext  $next
     */
    public function handle(UploadContext $context, Closure $next): UploadContext;
}
