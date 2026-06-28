<?php

declare(strict_types=1);

namespace App\Modules\Localization\Contracts;

use App\Modules\Localization\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Resolves which locale applies to a request and exposes the set of supported
 * locales, their fallbacks, and the public/internal alias mapping.
 */
interface LocaleResolverInterface
{
    /**
     * Every known locale (config-synthesised when the table is empty).
     *
     * @return Collection<int, Locale>
     */
    public function all(): Collection;

    /**
     * The active locale codes (config fallback when the table is empty).
     *
     * @return list<string>
     */
    public function codes(): array;

    public function default(): string;

    public function fallbackFor(string $locale): ?string;

    public function isSupported(string $locale): bool;

    /**
     * Map an alias to its internal code and validate; null when unsupported.
     */
    public function normalize(string $candidate): ?string;

    /**
     * The public-facing URL segment (alias) for an internal code.
     */
    public function publicSegment(string $code): string;

    /**
     * Resolve the active locale from a request: route segment, then
     * Accept-Language negotiation, then the default locale.
     */
    public function resolveFromRequest(Request $request): string;

    public function current(): string;
}
