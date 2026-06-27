<?php

use App\Modules\Media\Contracts\VirusScannerInterface;
use App\Modules\Media\Exceptions\DangerousFileException;
use App\Modules\Media\Exceptions\InvalidMediaFileException;
use App\Modules\Media\Exceptions\UnsupportedMediaTypeException;
use App\Modules\Media\Security\FileValidator;
use App\Modules\Media\Security\NullVirusScanner;
use Illuminate\Http\UploadedFile;

function makeFileValidator(int $maxSize = 10_485_760, ?array $allowedMimeTypes = null, array $disallowedExtensions = ['php'], ?VirusScannerInterface $scanner = null): FileValidator
{
    return new FileValidator($maxSize, $allowedMimeTypes, $disallowedExtensions, $scanner ?? new NullVirusScanner);
}

it('accepts a valid image', function () {
    makeFileValidator()->validate(UploadedFile::fake()->image('ok.jpg', 50, 50));
})->throwsNoExceptions();

it('rejects files larger than the configured maximum', function () {
    makeFileValidator(maxSize: 1000)->validate(UploadedFile::fake()->create('big.bin', 5));
})->throws(InvalidMediaFileException::class);

it('rejects a dangerous interior extension segment', function () {
    makeFileValidator(disallowedExtensions: ['php'])->validate(UploadedFile::fake()->create('shell.php.jpg', 1));
})->throws(DangerousFileException::class);

it('rejects a MIME type outside the allow-list', function () {
    makeFileValidator(allowedMimeTypes: ['image/png'])->validate(UploadedFile::fake()->image('a.jpg', 10, 10));
})->throws(UnsupportedMediaTypeException::class);

it('rejects a file flagged by the virus scanner', function () {
    $scanner = new class implements VirusScannerInterface
    {
        public function isEnabled(): bool
        {
            return true;
        }

        public function scan(string $absolutePath): void
        {
            throw DangerousFileException::virusDetected('EICAR-TEST');
        }
    };

    makeFileValidator(scanner: $scanner)->validate(UploadedFile::fake()->image('a.jpg', 10, 10));
})->throws(DangerousFileException::class);
