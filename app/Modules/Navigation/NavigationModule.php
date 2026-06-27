<?php

declare(strict_types=1);

namespace App\Modules\Navigation;

use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\ModuleInterface;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Modules\Navigation\Contracts\NavigationCacheInterface;
use App\Modules\Navigation\Contracts\NavigationGeneratorInterface;
use App\Modules\Navigation\Contracts\NavigationGeneratorRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Contracts\NavigationRepositoryInterface;
use App\Modules\Navigation\Contracts\NavigationSourceRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Contracts\NavigationTreeBuilderInterface;
use App\Modules\Navigation\Contracts\NavigationTypeRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationVisibilityEvaluatorInterface;
use App\Modules\Navigation\DTOs\NavigationTypeDefinition;
use App\Modules\Navigation\Enums\NavigationType;
use App\Modules\Navigation\Generators\PublishedEntriesGenerator;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Registries\NavigationGeneratorRegistry;
use App\Modules\Navigation\Registries\NavigationSourceRegistry;
use App\Modules\Navigation\Registries\NavigationTypeRegistry;
use App\Modules\Navigation\Repositories\EloquentNavigationRepository;
use App\Modules\Navigation\Services\NavigationCache;
use App\Modules\Navigation\Services\NavigationManager;
use App\Modules\Navigation\Services\NavigationSourceCache;
use App\Modules\Navigation\Services\NavigationTreeBuilder;
use App\Modules\Navigation\Services\NavigationVisibilityEvaluator;
use App\Modules\Navigation\Sources\CollectionSourceResolver;
use App\Modules\Navigation\Sources\EntrySourceResolver;
use App\Modules\Navigation\Sources\ExternalUrlSourceResolver;
use App\Modules\Navigation\Sources\ModulePageSourceResolver;
use App\Modules\Navigation\Sources\PluginPageSourceResolver;
use App\Modules\Navigation\Sources\RouteSourceResolver;
use App\Modules\Navigation\Sources\StaticUrlSourceResolver;
use App\Modules\Navigation\Support\NavigationHooks;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application;

/**
 * Bootstraps the Navigation Engine — the canonical menu system. Registered in
 * config/khf.php under `modules` (after `identity`, whose façade the visibility
 * evaluator consumes).
 */
final class NavigationModule implements ModuleInterface
{
    /** @var list<class-string<NavigationSourceResolverInterface>> */
    private const NATIVE_SOURCES = [
        EntrySourceResolver::class,
        CollectionSourceResolver::class,
        StaticUrlSourceResolver::class,
        ExternalUrlSourceResolver::class,
        RouteSourceResolver::class,
        ModulePageSourceResolver::class,
        PluginPageSourceResolver::class,
    ];

    /** @var list<class-string<NavigationGeneratorInterface>> */
    private const NATIVE_GENERATORS = [
        PublishedEntriesGenerator::class,
    ];

    public function __construct(private readonly Application $app) {}

    public function register(): void
    {
        $this->app->singleton(NavigationSourceRegistryInterface::class, NavigationSourceRegistry::class);
        $this->app->singleton(NavigationGeneratorRegistryInterface::class, NavigationGeneratorRegistry::class);
        $this->app->singleton(NavigationTypeRegistryInterface::class, NavigationTypeRegistry::class);

        $this->app->bind(NavigationRepositoryInterface::class, EloquentNavigationRepository::class);
        $this->app->singleton(NavigationVisibilityEvaluatorInterface::class, NavigationVisibilityEvaluator::class);
        $this->app->singleton(NavigationSourceCache::class);
        $this->app->singleton(NavigationTreeBuilderInterface::class, NavigationTreeBuilder::class);

        $this->app->singleton(NavigationCacheInterface::class, fn (): NavigationCache => new NavigationCache(
            $this->app->make(NavigationTreeBuilderInterface::class),
            $this->app->make(CacheFactory::class)->store(config('khf.navigation.cache_store')),
            (string) config('khf.navigation.cache_key', 'khf.navigation'),
            (int) config('khf.navigation.cache_ttl', 0),
        ));

        $this->app->singleton(NavigationManagerInterface::class, NavigationManager::class);
    }

    public function boot(): void
    {
        $types = $this->app->make(NavigationTypeRegistryInterface::class);
        foreach (NavigationType::cases() as $type) {
            $types->register(NavigationTypeDefinition::fromEnum($type));
        }

        $sources = $this->app->make(NavigationSourceRegistryInterface::class);
        foreach (self::NATIVE_SOURCES as $source) {
            $sources->register($this->app->make($source));
        }

        $generators = $this->app->make(NavigationGeneratorRegistryInterface::class);
        foreach (self::NATIVE_GENERATORS as $generator) {
            $generators->register($this->app->make($generator));
        }

        // Keep the cache coherent with every write through either navigation
        // model, and with changes to the Entry/Collection data baked into trees
        // (resolved URLs/labels + dynamic items). Coarse but correct; operators
        // who prefer a higher hit-rate over immediacy can set a cache TTL.
        $flush = function (): void {
            $this->app->make(NavigationCacheInterface::class)->flush();
        };
        Navigation::saved($flush);
        Navigation::deleted($flush);
        NavigationItem::saved($flush);
        NavigationItem::deleted($flush);
        Entry::saved($flush);
        Entry::deleted($flush);
        Collection::saved($flush);
        Collection::deleted($flush);

        // Extension point: plugins register types, sources and generators here.
        $hooks = $this->app->make(HookManagerInterface::class);
        if ($hooks->hasAction(NavigationHooks::REGISTER)) {
            $hooks->doAction(NavigationHooks::REGISTER, $this->app->make(NavigationManagerInterface::class));
        }
    }
}
