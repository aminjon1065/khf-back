<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Core\Events\EventBus;
use App\Core\Hooks\HookManager;
use App\Core\Support\ModuleLoader;
use App\Core\Support\ModuleRegistry;
use App\Core\Support\PluginLoader;
use App\Core\Support\PluginRegistry;
use Illuminate\Support\ServiceProvider;

/**
 * Central entry point for the KHF CMS engine.
 *
 * Responsibilities:
 *   1. Bind all Core contracts to their implementations as container singletons.
 *   2. Trigger module registration (module services bound to container).
 *   3. Trigger plugin registration (plugin services bound to container).
 *   4. Boot all modules (routes, listeners, scheduled commands registered).
 *   5. Boot all plugins (hooks and event listeners registered).
 *
 * This provider intentionally does not replace any existing Laravel machinery.
 * It sits alongside AppServiceProvider, wiring the CMS engine into the framework
 * without touching authentication, routing, or Eloquent.
 */
final class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class);
        $this->app->singleton(PluginRegistry::class);

        $this->app->singleton(HookManagerInterface::class, HookManager::class);
        $this->app->singleton(EventBusInterface::class, EventBus::class);

        $this->app->singleton(ModuleLoader::class);
        $this->app->singleton(PluginLoader::class);

        $this->app->make(ModuleLoader::class)->load(
            config('khf.modules', []),
        );

        $this->app->make(PluginLoader::class)->load(
            config('khf.plugins', []),
        );
    }

    public function boot(): void
    {
        $this->app->make(ModuleLoader::class)->boot();
        $this->app->make(PluginLoader::class)->boot();
    }
}
