<?php

declare(strict_types=1);

namespace App\Modules\Media\Security;

use App\Modules\Media\Contracts\FileValidatorInterface;
use App\Modules\Media\Contracts\VirusScannerInterface;
use App\Modules\Media\Exceptions\DangerousFileException;
use App\Modules\Media\Exceptions\InvalidMediaFileException;
use App\Modules\Media\Exceptions\UnsupportedMediaTypeException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileValidator implements FileValidatorInterface
{
    /**
     * @param  list<string>|null  $allowedMimeTypes  null = allow any non-dangerous type
     * @param  list<string>  $disallowedExtensions
     * @param  list<string>  $dangerousMimeTypes
     */
    public function __construct(
        private readonly int $maxSize,
        private readonly ?array $allowedMimeTypes,
        private readonly array $disallowedExtensions,
        private readonly VirusScannerInterface $virusScanner,
        private readonly array $dangerousMimeTypes = [],
    ) {}

    public function validate(File $file): void
    {
        $realPath = $file->getRealPath();

        if ($realPath === false || ! is_readable($realPath)) {
            throw InvalidMediaFileException::unreadable();
        }

        $size = $file->getSize();

        if ($size === false) {
            throw InvalidMediaFileException::unreadable();
        }

        if ($size > $this->maxSize) {
            throw InvalidMediaFileException::maxSizeExceeded($size, $this->maxSize);
        }

        // Name-based check on the client file name.
        $this->assertSafeName($this->originalName($file));

        // Content-based check: sniff the MIME from the actual bytes (finfo), NOT
        // the client-supplied / framework-guessed type, so a disguised SVG/HTML
        // payload cannot slip past as image/jpeg.
        $mimeType = $this->detectMimeType($realPath);

        if (in_array($mimeType, $this->dangerousMimeTypes, true)) {
            throw DangerousFileException::dangerousMimeType($mimeType);
        }

        if ($this->allowedMimeTypes !== null && ! in_array($mimeType, $this->allowedMimeTypes, true)) {
            throw UnsupportedMediaTypeException::mime($mimeType);
        }

        if ($this->virusScanner->isEnabled()) {
            $this->virusScanner->scan($realPath);
        }
    }

    private function detectMimeType(string $absolutePath): string
    {
        $detected = (new \finfo(FILEINFO_MIME_TYPE))->file($absolutePath);

        return is_string($detected) && $detected !== '' ? $detected : 'application/octet-stream';
    }

    private function originalName(File $file): string
    {
        return $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename();
    }

    /**
     * Reject a file whose name contains any dangerous extension segment, so
     * `shell.php.jpg` is blocked on its interior `php` segment as well.
     */
    private function assertSafeName(string $name): void
    {
        $segments = explode('.', strtolower($name));
        array_shift($segments); // drop the base name, keep every extension segment

        foreach ($segments as $segment) {
            if (in_array($segment, $this->disallowedExtensions, true)) {
                throw DangerousFileException::dangerousExtension($segment);
            }
        }
    }
}
