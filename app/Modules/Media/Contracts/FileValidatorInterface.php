<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Exceptions\DangerousFileException;
use App\Modules\Media\Exceptions\InvalidMediaFileException;
use App\Modules\Media\Exceptions\UnsupportedMediaTypeException;
use Symfony\Component\HttpFoundation\File\File;

interface FileValidatorInterface
{
    /**
     * Validate a candidate upload against size, MIME, extension and dangerous-file rules.
     *
     * @throws InvalidMediaFileException
     * @throws UnsupportedMediaTypeException
     * @throws DangerousFileException
     */
    public function validate(File $file): void;
}
