<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

/**
 * A pluggable setting type. Defines how a value is normalised for storage, cast
 * back on read, and what validation rules apply by default. New types are added
 * by implementing this and registering with the SettingTypeRegistry.
 */
interface SettingTypeInterface
{
    public function name(): string;

    /**
     * Normalise an input value into its JSON-serialisable storage form.
     */
    public function serialize(mixed $value): mixed;

    /**
     * Cast a stored (JSON-decoded) value into its typed representation.
     */
    public function cast(mixed $value): mixed;

    /**
     * Default validation rules for this type (merged with a setting's own rules).
     *
     * @return list<string>
     */
    public function rules(): array;
}
