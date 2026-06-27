<?php

namespace App\Http\Controllers\Admin;

use App\Core\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::latest()->paginate(24);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $media->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'file_name' => $item->file_name,
                        'url' => $item->url,
                        'mime_type' => $item->mime_type,
                        'size' => $item->size,
                    ];
                }),
                'next_page_url' => $media->nextPageUrl(),
            ]);
        }

        return Inertia::render('System/Media/Index', [
            'media' => $media->through(function ($item) {
                return [
                    'id' => $item->id,
                    'file_name' => $item->file_name,
                    'url' => $item->url,
                    'mime_type' => $item->mime_type,
                    'size' => $item->size,
                    'created_at' => $item->created_at->toDateTimeString(),
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // 20MB max
        ]);

        $file = $request->file('file');

        $path = $file->store('media', 'public');

        $media = Media::create([
            'file_name' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'user_id' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $media->id,
                'file_name' => $media->file_name,
                'url' => $media->url,
            ]);
        }

        return back()->with('success', 'File uploaded successfully.');
    }

    public function destroy(Media $medium)
    {
        Storage::disk($medium->disk)->delete($medium->path);
        $medium->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Deleted successfully']);
        }

        return back()->with('success', 'File deleted successfully.');
    }
}
