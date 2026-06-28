<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocaleResolverInterface;
use App\Modules\Localization\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

beforeEach(function (): void {
    Locale::factory()->create([
        'code' => 'tg', 'name' => 'Tajik', 'native_name' => 'Тоҷикӣ',
        'is_default' => true, 'is_active' => true, 'fallback_code' => null,
        'alias' => 'tj', 'sort_order' => 1,
    ]);
    Locale::factory()->create([
        'code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский',
        'is_default' => false, 'is_active' => true, 'fallback_code' => 'tg',
        'alias' => null, 'sort_order' => 2,
    ]);
    Locale::factory()->create([
        'code' => 'en', 'name' => 'English', 'native_name' => 'English',
        'is_default' => false, 'is_active' => true, 'fallback_code' => 'tg',
        'alias' => null, 'sort_order' => 3,
    ]);

    $this->resolver = app(LocaleResolverInterface::class);
});

/**
 * Build a request whose `locale` route parameter is bound to the given segment.
 *
 * @param  array<string, string>  $headers
 */
function requestWithLocaleSegment(?string $segment, array $headers = []): Request
{
    $request = Request::create('/', 'GET', server: $headers);

    $route = new Route(['GET'], '/{locale}', []);
    $route->bind($request);

    if ($segment !== null) {
        $route->setParameter('locale', $segment);
    }

    $request->setRouteResolver(static fn (): Route => $route);

    return $request;
}

it('lists the active locale codes', function (): void {
    expect($this->resolver->codes())->toBe(['tg', 'ru', 'en']);
});

it('reports the default locale code', function (): void {
    expect($this->resolver->default())->toBe('tg');
});

it('normalises a public alias to its internal code', function (): void {
    expect($this->resolver->normalize('tj'))->toBe('tg');
});

it('normalises a supported code to itself', function (): void {
    expect($this->resolver->normalize('ru'))->toBe('ru');
});

it('returns null when normalising an unsupported candidate', function (): void {
    expect($this->resolver->normalize('xx'))->toBeNull()
        ->and($this->resolver->normalize(''))->toBeNull();
});

it('exposes the public segment, applying the alias', function (): void {
    expect($this->resolver->publicSegment('tg'))->toBe('tj')
        ->and($this->resolver->publicSegment('ru'))->toBe('ru');
});

it('reports whether a locale is supported', function (): void {
    expect($this->resolver->isSupported('ru'))->toBeTrue()
        ->and($this->resolver->isSupported('tg'))->toBeTrue()
        ->and($this->resolver->isSupported('de'))->toBeFalse();
});

it('exposes the fallback for a locale', function (): void {
    expect($this->resolver->fallbackFor('ru'))->toBe('tg')
        ->and($this->resolver->fallbackFor('tg'))->toBeNull();
});

it('resolves the route segment alias to the internal locale', function (): void {
    $request = requestWithLocaleSegment('tj');

    expect($this->resolver->resolveFromRequest($request))->toBe('tg');
});

it('resolves a non-aliased route segment directly', function (): void {
    $request = requestWithLocaleSegment('ru');

    expect($this->resolver->resolveFromRequest($request))->toBe('ru');
});

it('negotiates the locale from Accept-Language when no segment is present', function (): void {
    $request = requestWithLocaleSegment(null, ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.9,en;q=0.8']);

    expect($this->resolver->resolveFromRequest($request))->toBe('ru');
});

it('falls back to the default locale when nothing matches', function (): void {
    $request = requestWithLocaleSegment('zz', ['HTTP_ACCEPT_LANGUAGE' => 'fr-FR,fr;q=0.9']);

    expect($this->resolver->resolveFromRequest($request))->toBe('tg');
});
