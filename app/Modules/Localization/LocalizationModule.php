<?php

declare(strict_types=1);

namespace App\Modules\Localization;

use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\ModuleInterface;
use App\Modules\Identity\Support\IdentityHooks;
use App\Modules\Localization\Contracts\LocaleRepositoryInterface;
use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\Contracts\LocalizationCacheInterface;
use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\Contracts\LocalizedSlugRepositoryInterface;
use App\Modules\Localization\Contracts\TranslationRepositoryInterface;
use App\Modules\Localization\Contracts\TranslationResolverInterface;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\Translation;
use App\Modules\Localization\Policies\LocalePolicy;
use App\Modules\Localization\Repositories\EloquentLocaleRepository;
use App\Modules\Localization\Repositories\EloquentLocalizedSlugRepository;
use App\Modules\Localization\Repositories\EloquentTranslationRepository;
use App\Modules\Localization\Services\FallbackResolver;
use App\Modules\Localization\Services\LocaleResolver;
use App\Modules\Localization\Services\LocalizationCache;
use App\Modules\Localization\Services\LocalizationService;
use App\Modules\Localization\Services\LocalizationValidator;
use App\Modules\Localization\Services\LocalizedSlugResolver;
use App\Modules\Localization\Services\LocalizedUrlGenerator;
use App\Modules\Localization\Services\TranslationResolver;
use App\Modules\Localization\Support\LocalizationHooks;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Gate;

/**
 * Bootstraps the Localization Engine — the canonical source of locales,
 * translations, localized slugs and URLs. Registered in config/khf.php under
 * `modules` (last, after `navigation`).
 */
final class LocalizationModule implements ModuleInterface
{
    public function __construct(private readonly Application $app) {}

    public function register(): void
    {
        $this->app->bind(LocaleRepositoryInterface::class, EloquentLocaleRepository::class);
        $this->app->bind(TranslationRepositoryInterface::class, EloquentTranslationRepository::class);
        $this->app->bind(LocalizedSlugRepositoryInterface::class, EloquentLocalizedSlugRepository::class);

        $this->app->singleton(LocalizationCacheInterface::class, fn (): LocalizationCache => new LocalizationCache(
            $this->app->make(LocaleRepositoryInterface::class),
            $this->app->make(TranslationRepositoryInterface::class),
            $this->app->make(CacheFactory::class)->store(config('khf.localization.cache_store')),
            (string) config('khf.localization.cache_key', 'khf.localization'),
            (int) config('khf.localization.cache_ttl', 0),
        ));

        $this->app->singleton(LocaleResolverInterface::class, LocaleResolver::class);
        $this->app->singleton(TranslationResolverInterface::class, TranslationResolver::class);

        $this->app->singleton(FallbackResolver::class);
        $this->app->singleton(LocalizedSlugResolver::class);
        $this->app->singleton(LocalizedUrlGenerator::class);
        $this->app->singleton(LocalizationValidator::class);

        $this->app->singleton(LocalizationServiceInterface::class, LocalizationService::class);
    }

    public function boot(): void
    {
        Gate::policy(Locale::class, LocalePolicy::class);

        // Keep the locale registry and translation maps coherent with any write
        // that goes straight through the engine models (bypassing the service).
        $flush = function (): void {
            $this->app->make(LocalizationCacheInterface::class)->flush();
        };
        Locale::saved($flush);
        Locale::deleted($flush);
        Translation::saved($flush);
        Translation::deleted($flush);

        // Contribute permissions to the Identity catalogue. Identity loads first,
        // so its REGISTER_PERMISSIONS filter is available here.
        $hooks = $this->app->make(HookManagerInterface::class);
        $hooks->addFilter(
            IdentityHooks::REGISTER_PERMISSIONS,
            /**
             * @param  list<string>  $permissions
             * @return list<string>
             */
            fn (array $permissions): array => array_merge($permissions, [
                'locales.view',
                'locales.create',
                'locales.update',
                'locales.delete',
                'translations.view',
                'translations.manage',
            ]),
        );

        // Extension point: plugins/modules register locale and translation
        // sources here. Only build the service when something is listening.
        if ($hooks->hasAction(LocalizationHooks::REGISTER)) {
            $hooks->doAction(LocalizationHooks::REGISTER, $this->app->make(LocalizationServiceInterface::class));
        }
    }
}
