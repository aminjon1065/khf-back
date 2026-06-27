<?php

declare(strict_types=1);

namespace App\Modules\Settings\ImportExport;

use App\Modules\Settings\Contracts\SettingsExporterInterface;
use App\Modules\Settings\Exceptions\SettingsException;

/**
 * Registry of format-specific exporters. New formats (YAML, XML, …) are added by
 * implementing SettingsExporterInterface and registering here — no manager change.
 */
final class ExporterManager
{
    /** @var array<string, SettingsExporterInterface> */
    private array $exporters = [];

    /**
     * @param  iterable<SettingsExporterInterface>  $exporters
     */
    public function __construct(iterable $exporters = [])
    {
        foreach ($exporters as $exporter) {
            $this->register($exporter);
        }
    }

    public function register(SettingsExporterInterface $exporter): void
    {
        $this->exporters[$exporter->format()] = $exporter;
    }

    public function get(string $format): SettingsExporterInterface
    {
        if (! isset($this->exporters[$format])) {
            throw new SettingsException("No settings exporter is registered for format [{$format}].");
        }

        return $this->exporters[$format];
    }
}
