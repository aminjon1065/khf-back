<?php

use App\Modules\Identity\IdentityModule;
use App\Modules\Media\MediaModule;
use App\Modules\Media\Storage\Drivers\LocalStorageDriver;
use App\Modules\Media\Storage\Drivers\S3StorageDriver;
use App\Modules\Navigation\NavigationModule;
use App\Modules\Settings\SettingsModule;

return [

    /*
    |--------------------------------------------------------------------------
    | Локали контента
    |--------------------------------------------------------------------------
    | Сегменты совпадают с фронтендом: tj (default) / ru / en. В backend язык
    | таджикского кодируем как «tg» (валидный ISO 639-1), как и на фронте.
    */
    'locales' => ['tg', 'ru', 'en'],

    'default_locale' => env('APP_LOCALE', 'tg'),

    /*
    |--------------------------------------------------------------------------
    | Доступ к API только для фронтенда
    |--------------------------------------------------------------------------
    | Next.js (серверно) шлёт этот токен в заголовке Authorization: Bearer
    | или X-Frontend-Token. Запросы без валидного токена отклоняются.
    */
    'frontend_api_token' => env('FRONTEND_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Ре-валидация Next (ISR)
    |--------------------------------------------------------------------------
    | Бекенд дёргает вебхук Next при публикации/правке контента.
    */
    'revalidate' => [
        'url' => env('NEXT_REVALIDATE_URL'),
        'secret' => env('NEXT_REVALIDATE_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | KHF Media Engine
    |--------------------------------------------------------------------------
    | Configuration for the canonical asset subsystem (app/Modules/Media).
    */
    'media' => [
        // Disk for public, web-served assets.
        'default_disk' => env('MEDIA_DISK', 'public'),

        // Disk for private assets — MUST NOT be web-served (default 'local' →
        // storage/app/private). Private bytes are reachable only via signed URLs.
        'private_disk' => env('MEDIA_PRIVATE_DISK', 'local'),

        'default_driver' => env('MEDIA_DRIVER', 'local'),

        // Pluggable storage drivers (FQCN). Add a driver here to register it.
        'drivers' => [
            LocalStorageDriver::class,
            S3StorageDriver::class,
        ],

        // spatie/image driver: 'imagick' (supports WebP + AVIF) or 'gd' (WebP only).
        'image_driver' => env('MEDIA_IMAGE_DRIVER', 'imagick'),

        'max_file_size' => (int) env('MEDIA_MAX_FILE_SIZE', 10 * 1024 * 1024), // 10 MB

        // null = accept any type that is not a dangerous extension or MIME.
        'allowed_mime_types' => null,

        // Content MIME types that are always rejected (XSS / executable vectors),
        // regardless of the allow-list, based on the file's sniffed content.
        'dangerous_mime_types' => [
            'image/svg+xml', 'text/html', 'application/xhtml+xml',
            'text/javascript', 'application/javascript',
            'application/x-httpd-php', 'text/x-php', 'application/x-php',
        ],

        // Extension segments rejected anywhere in a file name (client name AND
        // the content-derived extension).
        'disallowed_extensions' => [
            'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
            'exe', 'com', 'bat', 'cmd', 'sh', 'bash', 'cgi', 'pl', 'py', 'rb',
            'jar', 'msi', 'dll', 'so', 'dmg', 'app', 'htaccess', 'html', 'htm',
            'js', 'mjs', 'svg',
        ],

        // Default conversions generated for image uploads.
        'conversions' => [
            ['name' => 'thumbnail', 'width' => 320, 'height' => 320, 'fit' => 'crop', 'format' => 'webp', 'quality' => 80],
            ['name' => 'medium', 'width' => 1024, 'fit' => 'contain', 'format' => 'webp', 'quality' => 82],
        ],

        // Widths (px) for the responsive image set.
        'responsive_widths' => [320, 640, 1024, 1536],

        // Lifetime (minutes) for temporary/signed URLs.
        'temporary_url_lifetime' => (int) env('MEDIA_TEMP_URL_TTL', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | KHF Settings Engine
    |--------------------------------------------------------------------------
    | Configuration for the canonical settings subsystem (app/Modules/Settings).
    | Definitions/defaults live in code; only overridden values are persisted.
    */
    'settings' => [
        // Cache store for the resolved override map. null = the default store.
        'cache_store' => env('SETTINGS_CACHE_STORE'),

        // Cache key under which the full persisted-value map is held.
        'cache_key' => env('SETTINGS_CACHE_KEY', 'khf.settings'),

        // TTL in seconds for the cached map. 0 = cache forever (flushed on write).
        'cache_ttl' => (int) env('SETTINGS_CACHE_TTL', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | KHF Navigation Engine
    |--------------------------------------------------------------------------
    | Configuration for the canonical menu subsystem (app/Modules/Navigation).
    | Fully-built, multi-locale trees are cached; only writes invalidate them.
    */
    'navigation' => [
        // Cache store for the built navigation trees. null = the default store.
        'cache_store' => env('NAVIGATION_CACHE_STORE'),

        // Cache key under which the full handle => tree map is held.
        'cache_key' => env('NAVIGATION_CACHE_KEY', 'khf.navigation'),

        // TTL in seconds for the cached trees. 0 = cache forever (flushed on write).
        'cache_ttl' => (int) env('NAVIGATION_CACHE_TTL', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | KHF Engine — First-party Modules
    |--------------------------------------------------------------------------
    | Associative array of name => FQCN for each module to bootstrap.
    | Modules are loaded by ModuleLoader in two phases: register() then boot().
    |
    | Example:
    |   'identity' => \App\Modules\Identity\IdentityModule::class,
    */
    'modules' => [
        'identity' => IdentityModule::class,
        'media' => MediaModule::class,
        'settings' => SettingsModule::class,
        'navigation' => NavigationModule::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | KHF Engine — Third-party Plugins
    |--------------------------------------------------------------------------
    | Plain list of FQCNs for each plugin installed via Composer.
    | Plugins must implement PluginInterface and may only interact with the
    | system through the HookManager and EventBus.
    |
    | Example:
    |   \Acme\SeoPlugin\SeoPlugin::class,
    */
    'plugins' => [],

];
