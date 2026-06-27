<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Contracts\ModuleInterface;
use Illuminate\Contracts\Foundation\Application;

/**
 * Bootstraps first-party CMS modules in two phases.
 *
 * Phase 1 (register): instantiate each module and call register() so that module
 *   services are bound into the container before boot() runs anywhere.
 *
 * Phase 2 (boot): call boot() on every registered module in registration order.
 *   At this point the full container is available: routes, listeners, and scheduled
 *   commands can be registered safely.
 *
 * The module map is read from config('khf.modules') — an associative array of
 * name => FQCN pairs:
 *
 *   'modules' => [
 *       'identity' => IdentityModule::class,
 *       'content'  => ContentModule::class,
 *   ]
 */
final class ModuleLoader
{
    public function __construct(
        private readonly ModuleRegistry $registry,
        private readonly Application $app,
    ) {}

    /**
     * @param  array<string, class-string<ModuleInterface>>  $modules
     */
    public function load(array $modules): void
    {
        foreach ($modules as $name => $class) {
            /** @var ModuleInterface $module */
            $module = $this->app->make($class);
            $module->register();
            $this->registry->register($name, $module);
        }
    }

    public function boot(): void
    {
        foreach ($this->registry->all() as $module) {
            $module->boot();
        }
    }
}
