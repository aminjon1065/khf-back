<?php

declare(strict_types=1);

namespace App\Modules\Settings\ImportExport;

use App\Modules\Settings\Contracts\SettingsImporterInterface;
use App\Modules\Settings\Exceptions\SettingsException;

final class JsonImporter implements SettingsImporterInterface
{
    public function format(): string
    {
        return 'json';
    }

    /**
     * @return array<string, mixed>
     */
    public function import(string $payload): array
    {
        $data = json_decode($payload, true);

        // Settings import expects a JSON object of key => value pairs. A scalar,
        // null, or a (non-empty) JSON list is malformed and would otherwise create
        // garbage integer-keyed settings.
        if (! is_array($data) || ($data !== [] && array_is_list($data))) {
            throw new SettingsException('The settings payload must be a JSON object of key => value pairs.');
        }

        /** @var array<string, mixed> $data */
        return $data;
    }
}
