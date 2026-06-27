<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

use App\Modules\Settings\DTOs\SettingDefinition;
use Illuminate\Validation\ValidationException;

interface SettingsValidatorInterface
{
    /**
     * Validate a value against a definition's type rules + its own rules.
     *
     * @throws ValidationException
     */
    public function validate(SettingDefinition $definition, mixed $value): void;
}
