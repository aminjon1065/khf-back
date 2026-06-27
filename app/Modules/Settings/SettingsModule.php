<?php

declare(strict_types=1);

namespace App\Modules\Settings;

use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\ModuleInterface;
use App\Modules\Settings\Contracts\SettingsCacheInterface;
use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\Contracts\SettingsRegistryInterface;
use App\Modules\Settings\Contracts\SettingsRepositoryInterface;
use App\Modules\Settings\Contracts\SettingsValidatorInterface;
use App\Modules\Settings\Contracts\SettingTypeInterface;
use App\Modules\Settings\Contracts\SettingTypeRegistryInterface;
use App\Modules\Settings\DTOs\SettingGroup;
use App\Modules\Settings\ImportExport\ExporterManager;
use App\Modules\Settings\ImportExport\ImporterManager;
use App\Modules\Settings\ImportExport\JsonExporter;
use App\Modules\Settings\ImportExport\JsonImporter;
use App\Modules\Settings\Models\Setting;
use App\Modules\Settings\Registry\SettingsRegistry;
use App\Modules\Settings\Registry\SettingTypeRegistry;
use App\Modules\Settings\Repositories\EloquentSettingsRepository;
use App\Modules\Settings\Services\SettingsCache;
use App\Modules\Settings\Services\SettingsManager;
use App\Modules\Settings\Services\SettingsValidator;
use App\Modules\Settings\Support\SettingsHooks;
use App\Modules\Settings\Types\ArrayType;
use App\Modules\Settings\Types\BooleanType;
use App\Modules\Settings\Types\ColorType;
use App\Modules\Settings\Types\DateTimeType;
use App\Modules\Settings\Types\DateType;
use App\Modules\Settings\Types\EmailType;
use App\Modules\Settings\Types\EnumType;
use App\Modules\Settings\Types\FloatType;
use App\Modules\Settings\Types\IntegerType;
use App\Modules\Settings\Types\JsonType;
use App\Modules\Settings\Types\MediaType;
use App\Modules\Settings\Types\StringType;
use App\Modules\Settings\Types\UrlType;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application;

/**
 * Bootstraps the Settings Engine — the single source of truth for configurable
 * values. Registered in config/khf.php under `modules`.
 */
final class SettingsModule implements ModuleInterface
{
    /** @var list<class-string<SettingTypeInterface>> */
    private const NATIVE_TYPES = [
        StringType::class, IntegerType::class, FloatType::class, BooleanType::class,
        JsonType::class, ArrayType::class, DateType::class, DateTimeType::class,
        ColorType::class, EmailType::class, UrlType::class, MediaType::class, EnumType::class,
    ];

    /** @var array<string, string> name => label */
    private const DEFAULT_GROUPS = [
        'general' => 'General',
        'branding' => 'Branding',
        'localization' => 'Localization',
        'media' => 'Media',
        'seo' => 'SEO',
        'email' => 'Email',
        'security' => 'Security',
        'api' => 'API',
        'search' => 'Search',
        'workflow' => 'Workflow',
        'system' => 'System',
    ];

    public function __construct(private readonly Application $app) {}

    public function register(): void
    {
        $this->app->singleton(SettingsRegistryInterface::class, SettingsRegistry::class);
        $this->app->singleton(SettingTypeRegistryInterface::class, SettingTypeRegistry::class);
        $this->app->bind(SettingsRepositoryInterface::class, EloquentSettingsRepository::class);
        $this->app->singleton(SettingsValidatorInterface::class, SettingsValidator::class);

        $this->app->singleton(SettingsCacheInterface::class, fn (): SettingsCache => new SettingsCache(
            $this->app->make(SettingsRepositoryInterface::class),
            $this->app->make(CacheFactory::class)->store(config('khf.settings.cache_store')),
            (string) config('khf.settings.cache_key', 'khf.settings'),
            (int) config('khf.settings.cache_ttl', 0),
        ));

        $this->app->singleton(ExporterManager::class, fn (): ExporterManager => new ExporterManager([new JsonExporter]));
        $this->app->singleton(ImporterManager::class, fn (): ImporterManager => new ImporterManager([new JsonImporter]));

        $this->app->singleton(SettingsManagerInterface::class, SettingsManager::class);
    }

    public function boot(): void
    {
        $types = $this->app->make(SettingTypeRegistryInterface::class);
        foreach (self::NATIVE_TYPES as $type) {
            $types->register($this->app->make($type));
        }

        $registry = $this->app->make(SettingsRegistryInterface::class);
        foreach (self::DEFAULT_GROUPS as $name => $label) {
            $registry->registerGroup(new SettingGroup($name, $label));
        }

        // Keep the engine cache coherent with any write that bypasses the manager
        // and goes straight through the engine Setting model. Legacy singletons
        // live in a disjoint key-space (see EngineOwnedScope), so legacy writes
        // cannot affect the engine's value map and need no invalidation here.
        $flush = function (): void {
            $this->app->make(SettingsCacheInterface::class)->flush();
        };
        Setting::saved($flush);
        Setting::deleted($flush);

        // Extension point: plugins/modules register types, groups and definitions
        // here. Only build the manager when something is actually listening — this
        // keeps boot light and avoids eagerly resolving the event bus.
        $hooks = $this->app->make(HookManagerInterface::class);
        if ($hooks->hasAction(SettingsHooks::REGISTER)) {
            $hooks->doAction(SettingsHooks::REGISTER, $this->app->make(SettingsManagerInterface::class));
        }
    }
}
