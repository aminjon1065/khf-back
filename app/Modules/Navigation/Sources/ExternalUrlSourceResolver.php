<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Sources;

use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\NavigationItem;

/**
 * Resolves an absolute external URL (stored in source_value) verbatim — it is
 * locale-independent. Dangerous URL schemes (javascript:, data:, …) are rejected
 * so an author-supplied value cannot become a stored-XSS sink in the frontend.
 */
final class ExternalUrlSourceResolver implements NavigationSourceResolverInterface
{
    /** @var list<string> */
    private const ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    public function type(): string
    {
        return NavigationSourceType::ExternalUrl->value;
    }

    public function resolve(NavigationItem $item, string $locale): ?string
    {
        $value = $item->source_value;

        if ($value === null || $value === '' || ! $this->hasSafeScheme($value)) {
            return null;
        }

        return $value;
    }

    private function hasSafeScheme(string $url): bool
    {
        // Browsers strip leading/trailing ASCII whitespace and EVERY embedded tab,
        // CR and LF before resolving a URL's scheme — so "\tjavascript:alert(1)" or
        // "java\tscript:" would execute as javascript:. Normalize the same way
        // before the scheme check, otherwise parse_url() reports a null scheme and
        // the value would slip through as a "relative" URL.
        $normalized = preg_replace('/[\x00-\x20]+/', '', $url) ?? '';

        if ($normalized === '') {
            return false;
        }

        // Protocol-relative ("//host") and root-relative ("/path") URLs carry no
        // scheme and are safe.
        if (str_starts_with($normalized, '//') || str_starts_with($normalized, '/')) {
            return true;
        }

        $scheme = parse_url($normalized, PHP_URL_SCHEME);

        // A schemeless (relative) URL is safe; otherwise it must be allow-listed.
        return ! is_string($scheme) || in_array(strtolower($scheme), self::ALLOWED_SCHEMES, true);
    }
}
