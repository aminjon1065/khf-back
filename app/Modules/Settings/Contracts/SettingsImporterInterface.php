<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

/**
 * Parses a payload string into a key => value map for a given format.
 */
interface SettingsImporterInterface
{
    public function format(): string;

    /**
     * @return array<string, mixed>
     */
    public function import(string $payload): array;
}
