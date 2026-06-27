<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Contracts\PluginInterface;
use App\Core\Exceptions\CoreException;
use App\Core\Exceptions\PluginNotFoundException;

/**
 * Registry of all loaded CMS plugins.
 *
 * Keyed by the plugin's getName() value (e.g. "acme/seo-plugin").
 * Bound as a singleton by CoreServiceProvider.
 */
final class PluginRegistry
{
    /** @var array<string, PluginInterface> */
    private array $plugins = [];

    public function register(PluginInterface $plugin): void
    {
        $name = $plugin->getName();

        if ($this->has($name)) {
            throw new CoreException("Plugin [{$name}] is already registered. Each plugin name must be unique.");
        }

        $this->plugins[$name] = $plugin;
    }

    /**
     * @throws PluginNotFoundException when the plugin has not been registered
     */
    public function get(string $name): PluginInterface
    {
        if (! $this->has($name)) {
            throw new PluginNotFoundException("Plugin [{$name}] has not been registered.");
        }

        return $this->plugins[$name];
    }

    /**
     * @return array<string, PluginInterface>
     */
    public function all(): array
    {
        return $this->plugins;
    }

    public function has(string $name): bool
    {
        return isset($this->plugins[$name]);
    }

    /** @return list<string> */
    public function names(): array
    {
        return array_keys($this->plugins);
    }
}
