<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Enums;

/**
 * The built-in navigation locations. Further types are pluggable via the
 * NavigationTypeRegistry — this enum only seeds the native set.
 */
enum NavigationType: string
{
    case Header = 'header';
    case Footer = 'footer';
    case Sidebar = 'sidebar';
    case MegaMenu = 'mega_menu';
    case Breadcrumb = 'breadcrumb';
    case QuickLinks = 'quick_links';
    case Mobile = 'mobile';
    case Custom = 'custom';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $type): string => $type->value, self::cases());
    }

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }
}
