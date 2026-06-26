<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/media/index', [
            'items' => Media::query()
                ->latest()
                ->get()
                ->map(fn (Media $m): array => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'file_name' => $m->file_name,
                    'mime' => $m->mime_type,
                    'size' => $m->human_readable_size,
                    'url' => $m->getUrl(),
                    'preview' => str_starts_with((string) $m->mime_type, 'image/') ? $m->getUrl() : null,
                    'collection' => $m->collection_name,
                    'model' => class_basename((string) $m->model_type),
                    'created' => $m->created_at?->format('d.m.Y'),
                ]),
        ]);
    }

    public function destroy(Media $media): RedirectResponse
    {
        $media->delete();

        return back()->with('success', 'Файл удалён');
    }
}
