<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Enums\TextDirection;
use App\Modules\Localization\Support\LocalizationHooks;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Validates locale payloads and translation writes before persistence. Code
 * uniqueness and fallback existence are enforced by the service; this validator
 * checks shape, format, and that a translation targets a supported locale.
 */
final class LocalizationValidator
{
    public function __construct(
        private readonly ValidationFactory $validator,
        private readonly LocaleResolverInterface $locales,
        private readonly HookManagerInterface $hooks,
    ) {}

    /**
     * @throws ValidationException
     */
    public function validateLocale(LocaleData $data, ?string $ignoreCode = null): void
    {
        $rules = [
            'code' => ['required', 'string', 'max:12', 'regex:/^[a-z]{2}(-[A-Z]{2})?$/'],
            'name' => ['required', 'string'],
            'native_name' => ['required', 'string'],
            'direction' => ['required', Rule::in(TextDirection::values())],
            'fallback_code' => ['nullable', 'string'],
        ];

        $filtered = $this->hooks->applyFilters(LocalizationHooks::FILTER_VALIDATION_RULES, $rules, $data);

        if (is_array($filtered)) {
            $rules = $filtered;
        }

        $payload = [
            'code' => $data->code,
            'name' => $data->name,
            'native_name' => $data->nativeName,
            'direction' => $data->direction->value,
            'fallback_code' => $data->fallbackCode,
        ];

        $this->validator->make($payload, $rules)->validate();
    }

    /**
     * @throws ValidationException
     */
    public function validateTranslation(string $group, string $key, string $locale, ?string $value): void
    {
        /** @var array<string, mixed> $rules */
        $rules = [
            'group' => ['required', 'string'],
            'key' => ['required', 'string'],
            'locale' => ['required', 'string', Rule::in($this->locales->codes())],
            'value' => ['nullable', 'string'],
        ];

        $payload = [
            'group' => $group,
            'key' => $key,
            'locale' => $locale,
            'value' => $value,
        ];

        $this->validator->make($payload, $rules)->validate();
    }
}
