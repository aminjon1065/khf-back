<?php

declare(strict_types=1);

namespace App\Modules\Media\Services;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Modules\Media\Contracts\ImageProcessorInterface;
use App\Modules\Media\Contracts\MediaRepositoryInterface;
use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\Contracts\UploadPipelineInterface;
use App\Modules\Media\Contracts\UrlGeneratorInterface;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\DTOs\MediaData;
use App\Modules\Media\DTOs\UpdateMetadataData;
use App\Modules\Media\DTOs\UploadMediaData;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Events\MediaConverted;
use App\Modules\Media\Events\MediaDeleted;
use App\Modules\Media\Events\MediaOptimized;
use App\Modules\Media\Events\MediaRestored;
use App\Modules\Media\Events\MediaUpdated;
use App\Modules\Media\Events\MediaUploaded;
use App\Modules\Media\Exceptions\InvalidMediaFileException;
use App\Modules\Media\Exceptions\MediaException;
use App\Modules\Media\Exceptions\UnsupportedMediaTypeException;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use App\Modules\Media\Pipeline\UploadContext;
use App\Modules\Media\Support\MediaHooks;
use DateTimeInterface;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

final class MediaService implements MediaServiceInterface
{
    public function __construct(
        private readonly UploadPipelineInterface $pipeline,
        private readonly MediaRepositoryInterface $repository,
        private readonly MediaManager $manager,
        private readonly ImageProcessorInterface $imageProcessor,
        private readonly UrlGeneratorInterface $urls,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function upload(UploadMediaData $data): Media
    {
        $context = $this->buildContext($data);

        try {
            $context = $this->pipeline->process($context);
        } catch (Throwable $exception) {
            // Compensating cleanup: if the original was stored but persistence
            // failed, remove the orphaned bytes before rethrowing.
            if ($context->storedPath !== null && $context->media === null) {
                $this->manager->deleteStored($context->driver, $context->disk, $context->storedPath);
            }

            throw $exception;
        }

        $media = $context->media;

        if ($media === null) {
            throw new MediaException('The upload pipeline did not produce a media asset.');
        }

        $this->events->dispatch(new MediaUploaded($media));
        $this->hooks->doAction(MediaHooks::ACTION_UPLOADED, $media);

        if ($context->optimizedSize < $context->originalSize) {
            $this->events->dispatch(new MediaOptimized($media, $context->originalSize, $context->optimizedSize));
        }

        foreach ($context->generatedConversions as $conversion) {
            $this->events->dispatch(new MediaConverted($media, $conversion));
        }

        return $media->load('conversions');
    }

    public function updateMetadata(Media $media, UpdateMetadataData $data): Media
    {
        if ($data->name !== null) {
            $media->name = $data->name;
        }
        if ($data->altText !== null) {
            $media->alt_text = $data->altText;
        }
        if ($data->caption !== null) {
            $media->caption = $data->caption;
        }
        if ($data->copyright !== null) {
            $media->copyright = $data->copyright;
        }
        if ($data->focalPoint !== null) {
            $media->focal_point = $data->focalPoint;
        }
        if ($data->customProperties !== null) {
            $media->custom_properties = $data->customProperties;
        }

        $this->repository->save($media);
        $this->events->dispatch(new MediaUpdated($media));

        return $media;
    }

    public function delete(Media $media, bool $permanently = false): void
    {
        if ($permanently) {
            $media->loadMissing('conversions');
            $this->manager->deleteFiles($media);
        }

        $this->repository->delete($media, $permanently);
        $this->events->dispatch(new MediaDeleted($media, $permanently));
    }

    public function restore(Media $media): Media
    {
        $media = $this->repository->restore($media);
        $this->events->dispatch(new MediaRestored($media));

        return $media;
    }

    public function generateConversion(Media $media, ImageTransformation $transformation): MediaConversion
    {
        if (! $this->imageProcessor->isSupported($media->mime_type)) {
            throw UnsupportedMediaTypeException::mime($media->mime_type);
        }

        $temp = $this->manager->copyOriginalToTemp($media);

        try {
            $conversion = $this->manager->generateConversion($media, $temp, $transformation);
        } finally {
            if (is_file($temp)) {
                @unlink($temp);
            }
        }

        $this->events->dispatch(new MediaConverted($media, $conversion));
        $this->hooks->doAction(MediaHooks::ACTION_CONVERTED, $conversion);

        return $conversion;
    }

    public function url(Media $media): string
    {
        return $this->urls->url($media);
    }

    public function temporaryUrl(Media $media, DateTimeInterface $expiresAt): string
    {
        return $this->urls->temporaryUrl($media, $expiresAt);
    }

    public function toData(Media $media): MediaData
    {
        $media->loadMissing('conversions');

        return MediaData::fromModel($media, $this->urls->url($media));
    }

    private function buildContext(UploadMediaData $data): UploadContext
    {
        $file = $data->file;
        $realPath = $file->getRealPath();

        if ($realPath === false) {
            throw InvalidMediaFileException::unreadable();
        }

        $originalName = $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename();
        $extension = strtolower((string) ($file->guessExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION)));

        return new UploadContext(
            file: $file,
            mediaId: (string) Str::uuid(),
            sourcePath: $realPath,
            originalFileName: $originalName,
            fileName: $this->buildFileName($originalName, $extension),
            extension: $extension,
            mimeType: $file->getMimeType() ?? 'application/octet-stream',
            size: (int) $file->getSize(),
            disk: $data->disk ?? $this->defaultDiskFor($data->visibility),
            driver: $data->driver ?? (string) config('khf.media.default_driver'),
            visibility: $data->visibility,
            uploadedBy: $data->uploadedBy,
            name: $data->name,
            altText: $data->altText,
            caption: $data->caption,
            copyright: $data->copyright,
            customProperties: $data->customProperties,
            requestedConversions: $data->conversions,
        );
    }

    private function buildFileName(string $originalName, string $extension): string
    {
        $slug = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'file';

        return $extension !== '' ? "{$slug}.{$extension}" : $slug;
    }

    /**
     * Private assets default to a non-web-served disk so their bytes are
     * reachable only through a signed URL.
     */
    private function defaultDiskFor(MediaVisibility $visibility): string
    {
        return $visibility === MediaVisibility::Private
            ? (string) config('khf.media.private_disk', 'local')
            : (string) config('khf.media.default_disk', 'public');
    }
}
