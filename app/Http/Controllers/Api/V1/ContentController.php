<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Enums\EntryStatus;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EntryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContentController extends Controller
{
    public function index(Collection $collection): AnonymousResourceCollection
    {
        $locale = app()->getLocale();

        $entries = $collection->entries()
            ->where('status', EntryStatus::Published->value)
            ->hasLocale($locale)
            ->latest('published_at')
            ->paginate(15);

        return EntryResource::collection($entries);
    }

    public function show(Collection $collection, Entry $entry): EntryResource
    {
        $locale = app()->getLocale();
        // Ensure the entry belongs to the collection, is published, and has the requested locale
        if ($entry->collection_id !== $collection->id || $entry->status !== EntryStatus::Published) {
            abort(404);
        }

        // We could also check hasLocale manually here, but usually, a specific slug might just fallback
        // depending on business rules. Let's strictly enforce it:
        $data = $entry->data ?? [];
        if (! isset($data[$locale])) {
            abort(404, 'Entry not found for this locale.');
        }

        return new EntryResource($entry);
    }
}
