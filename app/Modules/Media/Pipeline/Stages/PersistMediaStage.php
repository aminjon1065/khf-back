<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline\Stages;

use App\Modules\Media\Contracts\MediaRepositoryInterface;
use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Factories\MediaFactory;
use App\Modules\Media\Pipeline\UploadContext;
use Closure;

final class PersistMediaStage implements PipelineStageInterface
{
    public function __construct(
        private readonly MediaFactory $factory,
        private readonly MediaRepositoryInterface $repository,
    ) {}

    public function handle(UploadContext $context, Closure $next): UploadContext
    {
        $context->media = $this->repository->save($this->factory->fromContext($context));

        return $next($context);
    }
}
