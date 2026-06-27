<?php

declare(strict_types=1);

namespace App\Modules\Settings\Repositories;

use App\Modules\Settings\Contracts\SettingsRepositoryInterface;
use App\Modules\Settings\Models\Setting;

final class EloquentSettingsRepository implements SettingsRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Setting::all()
            ->mapWithKeys(static fn (Setting $setting): array => [$setting->key => $setting->value])
            ->all();
    }

    public function has(string $key): bool
    {
        return Setting::query()->where('key', $key)->exists();
    }

    public function put(string $key, ?string $group, ?string $type, mixed $value): bool
    {
        $setting = Setting::query()->where('key', $key)->first();

        if ($setting === null) {
            Setting::query()->create([
                'key' => $key,
                'group' => $group,
                'type' => $type,
                'value' => $value,
            ]);

            return true; // created
        }

        // A null group/type means "leave the existing engine metadata untouched"
        // (e.g. an unregistered-path write to a key that was previously typed), so
        // we never strip the classification off a row the engine had already typed.
        $setting->value = $value;

        if ($group !== null) {
            $setting->group = $group;
        }

        if ($type !== null) {
            $setting->type = $type;
        }

        $setting->save();

        return false; // updated
    }

    public function forget(string $key): bool
    {
        return Setting::query()->where('key', $key)->delete() > 0;
    }
}
