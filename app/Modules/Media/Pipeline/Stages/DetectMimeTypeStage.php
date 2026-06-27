<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Pipeline\UploadContext;
use Closure;
use finfo;

/**
 * Re-derives the MIME type from the file's actual content (not the client-sent
 * type) so downstream stages and persistence trust an authoritative value.
 */
final class DetectMimeTypeStage implements PipelineStageInterface
{
    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        $detected = (new finfo(FILEINFO_MIME_TYPE))->file($context->sourcePath);

        if (is_string($detected) && $detected !== '') {
            $context->mimeType = $detected;
        }

        return $next($context);
    }
}
