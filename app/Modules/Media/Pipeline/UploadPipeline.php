<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline;

use App\Modules\Media\Contracts\PipelineStageInterface;
use App\Modules\Media\Contracts\UploadPipelineInterface;
use App\Modules\Media\Pipeline\Stages\DetectMimeTypeStage;
use App\Modules\Media\Pipeline\Stages\ExtractMetadataStage;
use App\Modules\Media\Pipeline\Stages\GenerateChecksumStage;
use App\Modules\Media\Pipeline\Stages\GenerateConversionsStage;
use App\Modules\Media\Pipeline\Stages\OptimizeImageStage;
use App\Modules\Media\Pipeline\Stages\PersistMediaStage;
use App\Modules\Media\Pipeline\Stages\StoreOriginalStage;
use App\Modules\Media\Pipeline\Stages\ValidateFileStage;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;

/**
 * Ordered, extensible upload pipeline. Stages are resolved from the container
 * (constructor injection) and can be extended at runtime via pipe().
 */
final class UploadPipeline implements UploadPipelineInterface
{
    /** @var list<class-string<PipelineStageInterface>> */
    private array $stages = [
        ValidateFileStage::class,
        DetectMimeTypeStage::class,
        ExtractMetadataStage::class,
        OptimizeImageStage::class,
        GenerateChecksumStage::class,
        StoreOriginalStage::class,
        PersistMediaStage::class,
        GenerateConversionsStage::class,
    ];

    public function __construct(private readonly Container $container) {}

    public function process(UploadContext $context): UploadContext
    {
        return (new Pipeline($this->container))
            ->send($context)
            ->through($this->stages)
            ->thenReturn();
    }

    public function pipe(string $stage): void
    {
        $this->stages[] = $stage;
    }
}
