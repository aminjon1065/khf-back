<?php

declare(strict_types=1);

namespace App\Modules\Settings\ImportExport;

use App\Modules\Settings\Contracts\SettingsImporterInterface;
use App\Modules\Settings\Exceptions\SettingsException;

/**
 * Registry of format-specific importers. New formats plug in without changing
 * the manager.
 */
final class ImporterManager
{
    /** @var array<string, SettingsImporterInterface> */
    private array $importers = [];

    /**
     * @param  iterable<SettingsImporterInterface>  $importers
     */
    public function __construct(iterable $importers = [])
    {
        foreach ($importers as $importer) {
            $this->register($importer);
        }
    }

    public function register(SettingsImporterInterface $importer): void
    {
        $this->importers[$importer->format()] = $importer;
    }

    public function get(string $format): SettingsImporterInterface
    {
        if (! isset($this->importers[$format])) {
            throw new SettingsException("No settings importer is registered for format [{$format}].");
        }

        return $this->importers[$format];
    }
}
