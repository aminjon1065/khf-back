<?php

namespace App\Enums;

/**
 * Семантический тон категории. На фронтенде маппится в Tailwind-класс
 * (NewsItem.categoryColor), поэтому backend хранит enum, а не класс.
 */
enum Tone: string
{
    case Alert = 'alert';
    case Brand = 'brand';
    case Success = 'success';
    case Warn = 'warn';

    public function textClass(): string
    {
        return match ($this) {
            self::Alert => 'text-alert',
            self::Brand => 'text-brand',
            self::Success => 'text-success',
            self::Warn => 'text-warn',
        };
    }
}
