<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Pipeline\UploadContext;
use App\Modules\Media\Services\MediaManager;
use App\Modules\Media\Support\MediaHooks;
use Closure;

final class StoreOriginalStage implements PipelineStageInterface
{
    public function __construct(
        private readonly MediaManager $manager,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        $path = $this->manager->originalPath($context->mediaId, $context->fileName);

        $this->manager->store($context->driver, $context->disk, $path, $context->sourcePath, $context->visibility);

        $context->storedPath = $path;

        $this->hooks->doAction(MediaHooks::ACTION_FILE_STORED, $context);

        return $next($context);
    }
}
