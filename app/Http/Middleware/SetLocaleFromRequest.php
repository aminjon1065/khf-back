<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Устанавливает локаль контента из ?lang или заголовка Accept-Language
 * (tg/ru/en — те же, что шлёт фронтенд через LANG_TAG).
 */
class SetLocaleFromRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var list<string> $supported */
        $supported = (array) config('khf.locales', ['tg']);

        $locale = $request->route('locale');

        // Forget the parameter so it doesn't get injected into all controller methods
        if ($request->route()) {
            $request->route()->forgetParameter('locale');
        }

        // Check if the provided locale is supported
        if (! is_string($locale) || ! in_array($locale, $supported, true)) {
            // Fall back to preferred language if route locale is invalid/missing
            $locale = $request->getPreferredLanguage($supported);
        }

        if (is_string($locale) && in_array($locale, $supported, true)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale((string) config('khf.default_locale', 'tg'));
        }

        return $next($request);
    }
}
