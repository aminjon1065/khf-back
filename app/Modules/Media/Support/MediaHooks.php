<?php

declare(strict_types=1);

namespace App\Modules\Media\Support;

/**
 * Extension points exposed by the Media Engine, fired through the Core
 * HookManager. Plugins use these to modify metadata, alter optimization,
 * register conversions, add watermarking, or connect AI processors.
 *
 * Action hooks observe lifecycle moments; filter hooks transform a value
 * in-flight (the documented `@filtered` type is what flows through them).
 */
final class MediaHooks
{
    /** @filtered array<string, mixed> — mutate extracted/descriptive metadata before persistence */
    public const string FILTER_METADATA = 'khf.media.metadata';

    /** @filtered list<array{name: string, width?: int, height?: int, format?: string, fit?: string, quality?: int}> — register or alter the conversions to generate */
    public const string FILTER_CONVERSIONS = 'khf.media.conversions';

    /** Action — after the original file is stored, before conversions (e.g. watermarking, AI tagging) */
    public const string ACTION_FILE_STORED = 'khf.media.file_stored';

    /** Action — after a media asset is fully uploaded and persisted */
    public const string ACTION_UPLOADED = 'khf.media.uploaded';

    /** Action — after a conversion is generated */
    public const string ACTION_CONVERTED = 'khf.media.converted';

    private function __construct() {}
}
