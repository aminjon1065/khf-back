<?php

declare(strict_types=1);

namespace App\Modules\Media\Security;

use App\Modules\Media\Contracts\VirusScannerInterface;

/**
 * Default no-op scanner. Deployments bind a real implementation (e.g. ClamAV)
 * to VirusScannerInterface to activate scanning without touching the pipeline.
 */
final class NullVirusScanner implements VirusScannerInterface
{
    public function isEnabled(): bool
    {
        return false;
    }

    public function scan(string $absolutePath): void
    {
        // Intentionally empty — no scanning is performed by the default binding.
    }
}
