<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Contracts\ModuleInterface;
use App\Core\Exceptions\CoreException;
use App\Core\Exceptions\ModuleNotFoundException;

/**
 * Registry of all loaded CMS modules.
 *
 * Bound as a singleton by CoreServiceProvider. Any class that needs to
 * introspect installed modules (admin UI, diagnostic commands) can inject
 * this registry directly.
 */
final class ModuleRegistry
{
    /** @var array<string, ModuleInterface> */
    private array $modules = [];

    public function register(string $name, ModuleInterface $module): void
    {
        if ($this->has($name)) {
            throw new CoreException("Module [{$name}] is already registered. Each module name must be unique.");
        }

        $this->modules[$name] = $module;
    }

    /**
     * @throws ModuleNotFoundException when the module has not been registered
     */
    public function get(string $name): ModuleInterface
    {
        if (! $this->has($name)) {
            throw new ModuleNotFoundException("Module [{$name}] has not been registered.");
        }

        return $this->modules[$name];
    }

    /**
     * @return array<string, ModuleInterface>
     */
    public function all(): array
    {
        return $this->modules;
    }

    public function has(string $name): bool
    {
        return isset($this->modules[$name]);
    }

    /** @return list<string> */
    public function names(): array
    {
        return array_keys($this->modules);
    }
}
