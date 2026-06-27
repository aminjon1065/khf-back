<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Modules\Settings\Contracts\SettingsCacheInterface;
use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\Contracts\SettingsRegistryInterface;
use App\Modules\Settings\Contracts\SettingsRepositoryInterface;
use App\Modules\Settings\Contracts\SettingsValidatorInterface;
use App\Modules\Settings\Contracts\SettingTypeInterface;
use App\Modules\Settings\Contracts\SettingTypeRegistryInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\DTOs\SettingGroup;
use App\Modules\Settings\Events\SettingCreated;
use App\Modules\Settings\Events\SettingDeleted;
use App\Modules\Settings\Events\SettingsExported;
use App\Modules\Settings\Events\SettingsImported;
use App\Modules\Settings\Events\SettingUpdated;
use App\Modules\Settings\Exceptions\SettingsException;
use App\Modules\Settings\ImportExport\ExporterManager;
use App\Modules\Settings\ImportExport\ImporterManager;
use App\Modules\Settings\Support\SettingsHooks;

/**
 * The public API of the Settings Engine. Resolves values (defaults overlaid with
 * persisted overrides), validates + persists writes, keeps the cache coherent,
 * and exposes the registration / import-export surface. Other components own the
 * sub-concerns; this manager orchestrates them.
 */
final class SettingsManager implements SettingsManagerInterface
{
    public function __construct(
        private readonly SettingsRegistryInterface $registry,
        private readonly SettingTypeRegistryInterface $types,
        private readonly SettingsRepositoryInterface $repository,
        private readonly SettingsCacheInterface $cache,
        private readonly SettingsValidatorInterface $validator,
        private readonly ExporterManager $exporters,
        private readonly ImporterManager $importers,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $values = $this->cache->values();
        $definition = $this->registry->definition($key);

        if (array_key_exists($key, $values)) {
            return $definition !== null
                ? $this->castOut($definition->type, $values[$key])
                : $values[$key];
        }

        if ($definition !== null) {
            return $this->castOut($definition->type, $definition->default);
        }

        return $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->cache->values()) || $this->registry->hasDefinition($key);
    }

    public function set(string $key, mixed $value): void
    {
        $this->assertEngineKey($key);

        $definition = $this->registry->definition($key);
        $serialized = $definition !== null
            ? $this->serializeIn($definition->type, $value)
            : $value; // Unregistered key: stored untyped, exactly as given.

        // No-op short-circuit: an identical persisted value is neither re-written
        // nor re-announced, so repeated writes (and idempotent re-imports) do not
        // spam SettingUpdated / the CHANGED hook.
        $stored = $this->cache->values();
        if (array_key_exists($key, $stored) && $stored[$key] === $serialized) {
            return;
        }

        $previous = $this->get($key);

        if ($definition !== null) {
            $this->validator->validate($definition, $value);
            $created = $this->repository->put($key, $definition->group, $definition->type, $serialized);
        } else {
            $created = $this->repository->put($key, null, null, $serialized);
        }

        $this->cache->flush();
        $current = $this->get($key);

        $this->events->dispatch($created
            ? new SettingCreated($key, $current)
            : new SettingUpdated($key, $current, $previous));
        $this->hooks->doAction(SettingsHooks::CHANGED, $key, $current, $previous);
    }

    public function forget(string $key): void
    {
        $this->assertEngineKey($key);

        $deleted = $this->repository->forget($key);
        $this->cache->flush();

        if ($deleted) {
            $this->events->dispatch(new SettingDeleted($key));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $resolved = [];

        foreach ($this->registry->definitions() as $key => $definition) {
            $resolved[$key] = $this->castOut($definition->type, $definition->default);
        }

        foreach ($this->cache->values() as $key => $stored) {
            $definition = $this->registry->definition($key);
            $resolved[$key] = $definition !== null ? $this->castOut($definition->type, $stored) : $stored;
        }

        return $resolved;
    }

    /**
     * Resolved values for every setting DEFINED in the given group. Group
     * membership is established by registration, so unregistered (untyped) keys
     * — which carry no group — are intentionally not part of any group here.
     *
     * @return array<string, mixed>
     */
    public function forGroup(string $group): array
    {
        $result = [];

        foreach ($this->registry->forGroup($group) as $definition) {
            $result[$definition->fullKey()] = $this->get($definition->fullKey());
        }

        return $result;
    }

    public function register(SettingDefinition $definition): void
    {
        $this->registry->register($definition);
    }

    public function registerGroup(SettingGroup $group): void
    {
        $this->registry->registerGroup($group);
    }

    public function registerType(SettingTypeInterface $type): void
    {
        $this->types->register($type);
    }

    public function warm(): void
    {
        $this->cache->warm();
    }

    public function flush(): void
    {
        $this->cache->flush();
    }

    public function export(string $format = 'json'): string
    {
        $values = $this->cache->values();
        $payload = $this->exporters->get($format)->export($values);

        $this->events->dispatch(new SettingsExported(count($values), $format));

        return $payload;
    }

    /**
     * @return list<string>
     */
    public function import(string $payload, string $format = 'json'): array
    {
        $data = $this->importers->get($format)->import($payload);
        $keys = [];

        foreach ($data as $key => $value) {
            $this->set((string) $key, $value);
            $keys[] = (string) $key;
        }

        $this->events->dispatch(new SettingsImported($keys, $format));

        return $keys;
    }

    /**
     * Writes address the engine's namespaced key-space ("{group}.{key}"). A bare
     * key would belong to the legacy App\Models\Setting singletons on the shared
     * table, which the engine must never overwrite or delete.
     */
    private function assertEngineKey(string $key): void
    {
        if (! str_contains($key, '.')) {
            throw new SettingsException("Setting keys must be namespaced as \"group.key\"; got [{$key}].");
        }
    }

    private function castOut(string $type, mixed $value): mixed
    {
        return $this->types->has($type) ? $this->types->get($type)->cast($value) : $value;
    }

    private function serializeIn(string $type, mixed $value): mixed
    {
        return $this->types->has($type) ? $this->types->get($type)->serialize($value) : $value;
    }
}
