<?php

declare(strict_types=1);

namespace App\Modules\Media\Imaging;

use App\Modules\Media\Contracts\ChecksumGeneratorInterface;
use App\Modules\Media\Exceptions\InvalidMediaFileException;

final class ChecksumGenerator implements ChecksumGeneratorInterface
{
    private const ALGORITHM = 'sha256';

    public function hash(string $absolutePath): string
    {
        $hash = hash_file(self::ALGORITHM, $absolutePath);

        if ($hash === false) {
            throw InvalidMediaFileException::unreadable();
        }

        return $hash;
    }

    public function algorithm(): string
    {
        return self::ALGORITHM;
    }
}
