<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\DTOs\ExtractedMetadata;

interface MetadataExtractorInterface
{
    public function extract(string $absolutePath, string $mimeType): ExtractedMetadata;
}
