<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

/**
 * A reference to a Media asset, stored as the media id (UUID string). Accepts a
 * Media model or an id; resolution to a Media model is the consumer's concern,
 * keeping the Settings engine decoupled from the Media module.
 */
final class MediaType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Media->value;
    }

    public function serialize(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_object($value) && method_exists($value, 'getKey')) {
            return (string) $value->getKey();
        }

        return (string) $value;
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (string) $value;
    }

    public function rules(): array
    {
        return ['string'];
    }
}
