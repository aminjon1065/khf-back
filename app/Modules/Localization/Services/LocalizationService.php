<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Modules\Localization\Contracts\LocaleRepositoryInterface;
use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\Contracts\LocalizationCacheInterface;
use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\Contracts\LocalizedSlugRepositoryInterface;
use App\Modules\Localization\Contracts\TranslationRepositoryInterface;
use App\Modules\Localization\Contracts\TranslationResolverInterface;
use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\DTOs\TranslationDTO;
use App\Modules\Localization\Events\LocaleCreated;
use App\Modules\Localization\Events\LocaleDeleted;
use App\Modules\Localization\Events\LocaleUpdated;
use App\Modules\Localization\Events\TranslationCreated;
use App\Modules\Localization\Events\TranslationDeleted;
use App\Modules\Localization\Events\TranslationUpdated;
use App\Modules\Localization\Exceptions\DuplicateLocaleException;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\LocalizedSlug;
use App\Modules\Localization\Support\LocalizationHooks;
use Illuminate\Support\Collection;

/**
 * The public API of the Localization Engine. Validates and persists locale,
 * translation, and slug writes; keeps the cache coherent; dispatches domain
 * events; and delegates resolution (translations, fallbacks, slugs, URLs) to the
 * dedicated resolvers. Other components own the sub-concerns; this service
 * orchestrates them.
 */
final class LocalizationService implements LocalizationServiceInterface
{
    public function __construct(
        private readonly LocaleRepositoryInterface $locales,
        private readonly TranslationRepositoryInterface $translations,
        private readonly LocalizedSlugRepositoryInterface $slugs,
        private readonly LocaleResolverInterface $localeResolver,
        private readonly TranslationResolverInterface $translationResolver,
        private readonly LocalizedSlugResolver $slugResolver,
        private readonly LocalizedUrlGenerator $urlGenerator,
        private readonly LocalizationCacheInterface $cache,
        private readonly LocalizationValidator $validator,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    /**
     * @return Collection<int, Locale>
     */
    public function locales(): Collection
    {
        return $this->cache->locales();
    }

    /**
     * @return list<string>
     */
    public function activeLocales(): array
    {
        return $this->localeResolver->codes();
    }

    public function defaultLocale(): string
    {
        return $this->localeResolver->default();
    }

    public function createLocale(LocaleData $data): Locale
    {
        $this->validator->validateLocale($data);

        if ($this->locales->find($data->code) !== null) {
            throw DuplicateLocaleException::code($data->code);
        }

        $this->assertFallbackExists($data->fallbackCode);

        if ($data->isDefault) {
            $this->locales->clearDefaultExcept($data->code);
        }

        $locale = $this->locales->create($data);
        $this->cache->flush();
        $this->events->dispatch(new LocaleCreated($locale));

        return $locale;
    }

    public function updateLocale(Locale $locale, LocaleData $data): Locale
    {
        $this->validator->validateLocale($data, $locale->code);

        $this->assertFallbackExists($data->fallbackCode);

        if ($data->isDefault) {
            $this->locales->clearDefaultExcept($data->code);
        }

        $updated = $this->locales->update($locale, $data);
        $this->cache->flush();
        $this->events->dispatch(new LocaleUpdated($updated));

        return $updated;
    }

    public function deleteLocale(Locale $locale): void
    {
        $code = $locale->code;

        $this->locales->delete($locale);
        $this->cache->flush();
        $this->events->dispatch(new LocaleDeleted($code));
    }

    public function translate(string $group, string $key, ?string $locale = null): ?string
    {
        return $this->translationResolver->resolve($group, $key, $locale);
    }

    public function setTranslation(string $group, string $key, string $locale, ?string $value): void
    {
        $this->validator->validateTranslation($group, $key, $locale, $value);

        $existing = $this->translations->get($group, $key, $locale);
        $previous = $existing?->value;

        $created = $this->translations->put($group, $key, $locale, $value);
        $this->cache->flushTranslations($locale);

        $dto = TranslationDTO::make($group, $key, $locale, $value);
        $this->events->dispatch($created
            ? new TranslationCreated($dto)
            : new TranslationUpdated($dto, $previous));

        $this->hooks->doAction(LocalizationHooks::TRANSLATION_CHANGED, $group, $key, $locale, $value);
    }

    public function forgetTranslation(string $group, string $key, string $locale): void
    {
        $deleted = $this->translations->forget($group, $key, $locale);
        $this->cache->flushTranslations($locale);

        if ($deleted) {
            $this->events->dispatch(new TranslationDeleted($group, $key, $locale));
        }
    }

    /**
     * @return array<string, string>
     */
    public function translations(string $locale, ?string $group = null): array
    {
        return $group !== null
            ? $this->translationResolver->forLocale($group, $locale)
            : $this->cache->translations($locale);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function resolve(array $values, ?string $locale = null, ?string $context = null): mixed
    {
        return $this->translationResolver->resolveValue($values, $locale, $context);
    }

    public function localizedSlug(string $subjectType, string $subjectId, ?string $locale = null): ?string
    {
        return $this->slugResolver->resolve($subjectType, $subjectId, $locale);
    }

    public function setSlug(string $subjectType, string $subjectId, string $locale, string $slug, bool $canonical = false): LocalizedSlug
    {
        return $this->slugs->put($subjectType, $subjectId, $locale, $slug, $canonical);
    }

    public function uniqueSlug(string $subjectType, string $locale, string $base, ?int $ignoreId = null): string
    {
        return $this->slugResolver->unique($subjectType, $locale, $base, $ignoreId);
    }

    public function url(string $path, ?string $locale = null): string
    {
        return $this->urlGenerator->to($path, $locale);
    }

    /**
     * @return array<string, string>
     */
    public function urlsForPath(string $path): array
    {
        return $this->urlGenerator->urlsForPath($path);
    }

    /**
     * Ensure a referenced fallback locale exists before persisting, keeping the
     * soft `fallback_code` reference pointing at a real locale.
     */
    private function assertFallbackExists(?string $fallbackCode): void
    {
        if ($fallbackCode === null || $fallbackCode === '') {
            return;
        }

        if ($this->locales->find($fallbackCode) === null) {
            throw new LocalizationException("Fallback locale [{$fallbackCode}] is not registered.");
        }
    }
}
