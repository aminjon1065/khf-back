<?php

declare(strict_types=1);

namespace App\Modules\Settings\ImportExport;

use App\Modules\Settings\Contracts\SettingsExporterInterface;

final class JsonExporter implements SettingsExporterInterface
{
    public function format(): string
    {
        return 'json';
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function export(array $settings): string
    {
        return json_encode(
            $settings,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) ?: '{}';
    }
}
