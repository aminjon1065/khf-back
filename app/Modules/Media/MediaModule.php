<?php

declare(strict_types=1);

namespace App\Modules\Media;

use App\Core\Contracts\ModuleInterface;
use App\Modules\Media\Contracts\ChecksumGeneratorInterface;
use App\Modules\Media\Contracts\FileValidatorInterface;
use App\Modules\Media\Contracts\ImageProcessorInterface;
use App\Modules\Media\Contracts\MediaRepositoryInterface;
use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\Contracts\MetadataExtractorInterface;
use App\Modules\Media\Contracts\StorageDriverInterface;
use App\Modules\Media\Contracts\StorageManagerInterface;
use App\Modules\Media\Contracts\UploadPipelineInterface;
use App\Modules\Media\Contracts\UrlGeneratorInterface;
use App\Modules\Media\Contracts\VirusScannerInterface;
use App\Modules\Media\Http\Controllers\MediaConversionDownloadController;
use App\Modules\Media\Http\Controllers\MediaDownloadController;
use App\Modules\Media\Imaging\ChecksumGenerator;
use App\Modules\Media\Imaging\MetadataExtractor;
use App\Modules\Media\Imaging\SpatieImageProcessor;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Pipeline\UploadPipeline;
use App\Modules\Media\Policies\MediaPolicy;
use App\Modules\Media\Repositories\EloquentMediaRepository;
use App\Modules\Media\Security\FileValidator;
use App\Modules\Media\Security\NullVirusScanner;
use App\Modules\Media\Services\MediaService;
use App\Modules\Media\Storage\StorageManager;
use App\Modules\Media\Url\UrlGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/**
 * Bootstraps the Media Engine. Registered in config/khf.php under `modules`
 * and loaded by the Core ModuleLoader (register() then boot()).
 */
final class MediaModule implements ModuleInterface
{
    public function __construct(private readonly Application $app) {}

    public function register(): void
    {
        $this->app->singleton(StorageManagerInterface::class, function (): StorageManager {
            $drivers = [];

            foreach ($this->stringList(config('khf.media.drivers', [])) as $driverClass) {
                $driver = $this->app->make($driverClass);

                if ($driver instanceof StorageDriverInterface) {
                    $drivers[] = $driver;
                }
            }

            return new StorageManager($drivers, (string) config('khf.media.default_driver', 'local'));
        });

        $this->app->singleton(ImageProcessorInterface::class, fn (): SpatieImageProcessor => new SpatieImageProcessor(
            (string) config('khf.media.image_driver', 'imagick'),
        ));

        $this->app->singleton(MetadataExtractorInterface::class, MetadataExtractor::class);
        $this->app->singleton(ChecksumGeneratorInterface::class, ChecksumGenerator::class);
        $this->app->singleton(VirusScannerInterface::class, NullVirusScanner::class);

        $this->app->singleton(FileValidatorInterface::class, fn (): FileValidator => new FileValidator(
            maxSize: (int) config('khf.media.max_file_size', 10 * 1024 * 1024),
            allowedMimeTypes: $this->stringListOrNull(config('khf.media.allowed_mime_types')),
            disallowedExtensions: $this->stringList(config('khf.media.disallowed_extensions', [])),
            virusScanner: $this->app->make(VirusScannerInterface::class),
            dangerousMimeTypes: $this->stringList(config('khf.media.dangerous_mime_types', [])),
        ));

        $this->app->singleton(UrlGeneratorInterface::class, fn (): UrlGenerator => new UrlGenerator(
            $this->app->make(StorageManagerInterface::class),
            (int) config('khf.media.temporary_url_lifetime', 5),
        ));

        $this->app->bind(MediaRepositoryInterface::class, EloquentMediaRepository::class);
        $this->app->singleton(UploadPipelineInterface::class, UploadPipeline::class);
        $this->app->singleton(MediaServiceInterface::class, MediaService::class);
    }

    public function boot(): void
    {
        Gate::policy(Media::class, MediaPolicy::class);

        if (! $this->app->routesAreCached()) {
            Route::middleware('web')->group(function (): void {
                Route::get('media/download/{media}', MediaDownloadController::class)->name('media.download');
                Route::get('media/conversion/{conversion}', MediaConversionDownloadController::class)->name('media.conversion.download');
            });
        }
    }

    /**
     * @return list<string>|null
     */
    private function stringListOrNull(mixed $value): ?array
    {
        return is_array($value) ? $this->stringList($value) : null;
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        return is_array($value) ? array_values(array_map(strval(...), $value)) : [];
    }
}
