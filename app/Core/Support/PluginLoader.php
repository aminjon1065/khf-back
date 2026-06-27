<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Contracts\PluginInterface;
use Illuminate\Contracts\Foundation\Application;

/**
 * Bootstraps third-party CMS plugins in two phases.
 *
 * Mirrors the same register → boot lifecycle as ModuleLoader.
 * Plugins interact with the system exclusively through the HookManager and
 * EventBus — they cannot overwrite Core files or call internal module APIs.
 *
 * The plugin list is read from config('khf.plugins') — a plain list of FQCNs:
 *
 *   'plugins' => [
 *       \Acme\SeoPlugin\SeoPlugin::class,
 *   ]
 */
final class PluginLoader
{
    public function __construct(
        private readonly PluginRegistry $registry,
        private readonly Application $app,
    ) {}

    /**
     * @param  array<class-string<PluginInterface>>  $plugins
     */
    public function load(array $plugins): void
    {
        foreach ($plugins as $class) {
            /** @var PluginInterface $plugin */
            $plugin = $this->app->make($class);
            $plugin->register();
            $this->registry->register($plugin);
        }
    }

    public function boot(): void
    {
        foreach ($this->registry->all() as $plugin) {
            $plugin->boot();
        }
    }
}
