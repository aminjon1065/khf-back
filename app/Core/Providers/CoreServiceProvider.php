<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\Schema\BlueprintRepositoryInterface;
use App\Core\Contracts\Schema\EntryRepositoryInterface;
use App\Core\Contracts\Schema\FieldTypeInterface;
use App\Core\Contracts\Schema\FieldTypeRegistryInterface;
use App\Core\Contracts\Schema\SchemaEngineInterface;
use App\Core\Events\EventBus;
use App\Core\Hooks\HookManager;
use App\Core\Repositories\EloquentBlueprintRepository;
use App\Core\Repositories\EloquentEntryRepository;
use App\Core\Schema\FieldTypeRegistry;
use App\Core\Schema\FieldTypes\BooleanFieldType;
use App\Core\Schema\FieldTypes\DateFieldType;
use App\Core\Schema\FieldTypes\DateTimeFieldType;
use App\Core\Schema\FieldTypes\JsonFieldType;
use App\Core\Schema\FieldTypes\MediaFieldType;
use App\Core\Schema\FieldTypes\MultiSelectFieldType;
use App\Core\Schema\FieldTypes\NumberFieldType;
use App\Core\Schema\FieldTypes\RelationFieldType;
use App\Core\Schema\FieldTypes\RichTextFieldType;
use App\Core\Schema\FieldTypes\SelectFieldType;
use App\Core\Schema\FieldTypes\TextareaFieldType;
use App\Core\Schema\FieldTypes\TextFieldType;
use App\Core\Schema\SchemaEngine;
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

        $this->registerSchemaEngine();

        $this->app->make(ModuleLoader::class)->load(
            config('khf.modules', []),
        );

        $this->app->make(PluginLoader::class)->load(
            config('khf.plugins', []),
        );
    }

    /**
     * Bind the Schema Engine: repositories, the pluggable field-type registry
     * seeded with the twelve built-in types, and the engine façade.
     */
    private function registerSchemaEngine(): void
    {
        $this->app->bind(BlueprintRepositoryInterface::class, EloquentBlueprintRepository::class);
        $this->app->bind(EntryRepositoryInterface::class, EloquentEntryRepository::class);

        $this->app->singleton(FieldTypeRegistryInterface::class, function (): FieldTypeRegistry {
            $registry = new FieldTypeRegistry;

            foreach ($this->builtInFieldTypes() as $fieldType) {
                $registry->register(new $fieldType);
            }

            return $registry;
        });

        $this->app->singleton(SchemaEngineInterface::class, SchemaEngine::class);
    }

    /**
     * The twelve field types shipped with the Schema Engine. Future modules add
     * their own by resolving FieldTypeRegistryInterface and calling register().
     *
     * @return list<class-string<FieldTypeInterface>>
     */
    private function builtInFieldTypes(): array
    {
        return [
            TextFieldType::class,
            TextareaFieldType::class,
            RichTextFieldType::class,
            NumberFieldType::class,
            BooleanFieldType::class,
            DateFieldType::class,
            DateTimeFieldType::class,
            SelectFieldType::class,
            MultiSelectFieldType::class,
            MediaFieldType::class,
            RelationFieldType::class,
            JsonFieldType::class,
        ];
    }

    public function boot(): void
    {
        $this->app->make(ModuleLoader::class)->boot();
        $this->app->make(PluginLoader::class)->boot();
    }
}
