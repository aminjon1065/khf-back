<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

interface ChecksumGeneratorInterface
{
    public function hash(string $absolutePath): string;

    public function algorithm(): string;
}
