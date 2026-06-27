<?php

declare(strict_types=1);

namespace App\Modules\Media\Http\Controllers;

use App\Modules\Media\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a stored asset behind a signed URL — the delivery mechanism for
 * private/temporary URLs on storage drivers without native pre-signing (local).
 * Storage access here is permitted because this controller lives inside the
 * Media module.
 */
final class MediaDownloadController
{
    public function __invoke(Request $request, Media $media): StreamedResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        return Storage::disk($media->disk)->download($media->path, $media->original_file_name);
    }
}
