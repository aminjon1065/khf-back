<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Media\Contracts\MediaRepositoryInterface;
use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\DTOs\UploadMediaData;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Exceptions\MediaException;
use App\Modules\Media\Http\Resources\MediaResource;
use App\Modules\Media\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin media endpoints. Interacts with assets ONLY through the Media Engine —
 * never Laravel Storage or Spatie.
 */
class MediaController extends Controller
{
    public function __construct(
        private readonly MediaServiceInterface $media,
        private readonly MediaRepositoryInterface $repository,
    ) {}

    public function index(Request $request): Response|JsonResponse
    {
        $paginator = $this->repository->paginate(24);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => collect($paginator->items())
                    ->map(fn (Media $item): array => MediaResource::make($item)->resolve($request)),
                'next_page_url' => $paginator->nextPageUrl(),
            ]);
        }

        return Inertia::render('System/Media/Index', [
            'media' => $paginator->through(fn (Media $item): array => MediaResource::make($item)->resolve($request)),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // 20 MB hard ceiling at the HTTP edge
        ]);

        try {
            $media = $this->media->upload(new UploadMediaData(
                file: $request->file('file'),
                visibility: MediaVisibility::Public,
                uploadedBy: $request->user()?->id,
            ));
        } catch (MediaException $exception) {
            throw ValidationException::withMessages(['file' => $exception->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json(MediaResource::make($media)->resolve($request));
        }

        return back()->with('success', 'File uploaded successfully.');
    }

    public function destroy(Request $request, Media $medium): RedirectResponse|JsonResponse
    {
        $this->media->delete($medium);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Deleted successfully']);
        }

        return back()->with('success', 'File deleted successfully.');
    }
}
