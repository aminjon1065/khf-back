<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Exceptions\DangerousFileException;

/**
 * Integration point for antivirus scanning. The engine ships a no-op default;
 * deployments bind a real scanner (e.g. ClamAV) without touching the pipeline.
 */
interface VirusScannerInterface
{
    public function isEnabled(): bool;

    /**
     * @throws DangerousFileException when the file is flagged as malicious
     */
    public function scan(string $absolutePath): void;
}
