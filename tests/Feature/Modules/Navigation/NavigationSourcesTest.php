<?php

declare(strict_types=1);

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Navigation\Contracts\NavigationSourceRegistryInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Support\NavigationHooks;
use Illuminate\Support\Facades\Route;

function navResolver(NavigationSourceType $type)
{
    return app(NavigationSourceRegistryInterface::class)->get($type->value);
}

function navItem(string $value): NavigationItem
{
    return new NavigationItem(['source_value' => $value]);
}

it('resolves a named route that declares a {locale} parameter', function () {
    Route::get('/{locale}/docs', fn () => '')->name('nav.docs');
    Route::getRoutes()->refreshNameLookups();

    expect(navResolver(NavigationSourceType::Route)->resolve(navItem('nav.docs'), 'en'))->toBe('/en/docs');
});

it('resolves a locale-agnostic named route without adding a locale segment', function () {
    Route::get('/about-us', fn () => '')->name('nav.about');
    Route::getRoutes()->refreshNameLookups();

    expect(navResolver(NavigationSourceType::Route)->resolve(navItem('nav.about'), 'ru'))->toBe('/about-us');
});

it('returns null for an unknown route name', function () {
    expect(navResolver(NavigationSourceType::Route)->resolve(navItem('nav.missing'), 'en'))->toBeNull();
});

it('resolves a module page through the MODULE_PAGE_URL filter', function () {
    app(HookManagerInterface::class)->addFilter(
        NavigationHooks::MODULE_PAGE_URL,
        fn (mixed $url, string $key, string $locale): ?string => $key === 'dashboard' ? "/{$locale}/dashboard" : $url,
    );

    expect(navResolver(NavigationSourceType::ModulePage)->resolve(navItem('dashboard'), 'ru'))->toBe('/ru/dashboard')
        ->and(navResolver(NavigationSourceType::ModulePage)->resolve(navItem('unmapped'), 'ru'))->toBeNull();
});

it('resolves a plugin page through the PLUGIN_PAGE_URL filter', function () {
    app(HookManagerInterface::class)->addFilter(
        NavigationHooks::PLUGIN_PAGE_URL,
        fn (mixed $url, string $key, string $locale): ?string => "/{$locale}/plugin/{$key}",
    );

    expect(navResolver(NavigationSourceType::PluginPage)->resolve(navItem('shop'), 'en'))->toBe('/en/plugin/shop');
});

it('rejects dangerous URL schemes for external links', function (string $dangerous) {
    expect(navResolver(NavigationSourceType::ExternalUrl)->resolve(navItem($dangerous), 'en'))->toBeNull();
})->with([
    'javascript:alert(1)',
    'JavaScript:alert(1)',
    'data:text/html,<script>x</script>',
    'vbscript:msgbox(1)',
    'file:///etc/passwd',
    // Browsers strip these chars before resolving the scheme, so they must not
    // become a bypass of the allow-list.
    ' javascript:alert(1)',
    "\tjavascript:alert(1)",
    "\njavascript:alert(1)",
    "java\tscript:alert(1)",
    '  data:text/html,x',
]);

it('allows safe external and relative URLs', function (string $safe) {
    expect(navResolver(NavigationSourceType::ExternalUrl)->resolve(navItem($safe), 'en'))->toBe($safe);
})->with([
    'https://example.com',
    'http://x.test/path',
    'mailto:a@b.test',
    'tel:+992900000000',
    '//cdn.test/asset.js',
    '/relative/path',
]);
