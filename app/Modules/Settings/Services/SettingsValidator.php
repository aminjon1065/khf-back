<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Settings\Contracts\SettingsValidatorInterface;
use App\Modules\Settings\Contracts\SettingTypeRegistryInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Support\SettingsHooks;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

/**
 * Validates a value against the type's default rules plus the setting's own
 * rules (and any rules plugins inject via the FILTER_RULES hook), before
 * persistence.
 */
final class SettingsValidator implements SettingsValidatorInterface
{
    public function __construct(
        private readonly SettingTypeRegistryInterface $types,
        private readonly HookManagerInterface $hooks,
        private readonly ValidationFactory $factory,
    ) {}

    public function validate(SettingDefinition $definition, mixed $value): void
    {
        $typeRules = $this->types->has($definition->type)
            ? $this->types->get($definition->type)->rules()
            : [];

        $rules = array_merge($typeRules, $definition->rules);

        $filtered = $this->hooks->applyFilters(SettingsHooks::FILTER_RULES, $rules, $definition);
        if (is_array($filtered)) {
            $rules = array_values(array_map(strval(...), $filtered));
        }

        $this->factory->make(['value' => $value], ['value' => $rules])->validate();
    }
}
