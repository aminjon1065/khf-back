<?php

declare(strict_types=1);

namespace App\Modules\Media\Http\Controllers;

use App\Modules\Media\Models\MediaConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a private conversion (derivative) behind a signed URL. Mirrors the
 * original-asset download controller for derivatives.
 */
final class MediaConversionDownloadController
{
    public function __invoke(Request $request, MediaConversion $conversion): StreamedResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $name = $conversion->conversion_name.'.'.$conversion->format;

        return Storage::disk($conversion->disk)->download($conversion->path, $name);
    }
}
