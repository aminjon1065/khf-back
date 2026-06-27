<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

/**
 * Serialises a key => value map to a string for a given format. Implementations
 * register under a format name; new formats (YAML, XML, …) plug in without
 * changing the manager.
 */
interface SettingsExporterInterface
{
    public function format(): string;

    /**
     * @param  array<string, mixed>  $settings
     */
    public function export(array $settings): string;
}
