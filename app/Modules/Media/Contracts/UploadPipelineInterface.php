<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Pipeline\UploadContext;

interface UploadPipelineInterface
{
    public function process(UploadContext $context): UploadContext;

    /**
     * Append an extra stage to the pipeline (extension point).
     *
     * @param  class-string<PipelineStageInterface>  $stage
     */
    public function pipe(string $stage): void;
}
