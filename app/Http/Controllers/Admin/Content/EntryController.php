<?php

namespace App\Http\Controllers\Admin\Content;

use App\Core\Contracts\Schema\SchemaEngineInterface;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Content\EntryRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EntryController extends Controller
{
    public function __construct(private readonly SchemaEngineInterface $engine) {}

    public function index(Collection $collection): Response
    {
        $entries = $collection->entries()->with('author')->latest()->paginate(20);

        return Inertia::render('Content/Entries/Index', [
            'collection' => $collection,
            'entries' => $entries,
        ]);
    }

    public function create(Collection $collection): Response
    {
        // For MVP, we assume the first blueprint is used. Later we can pass ?blueprint_id=
        $blueprint = $collection->blueprints()->with('fields')->firstOrFail();

        return Inertia::render('Content/Entries/Form', [
            'collection' => $collection,
            'blueprint' => $blueprint,
            'entry' => null,
            'supportedLocales' => ['tg', 'ru', 'en'], // Hardcoded for MVP, can be moved to config
        ]);
    }

    public function store(EntryRequest $request, Collection $collection): RedirectResponse
    {
        $this->engine->createEntry($request->toCreateData($collection, $request->user()?->id));

        return redirect()->route('admin.content.collections.entries.index', $collection)
            ->with('success', 'Entry created successfully.');
    }

    public function edit(Entry $entry): Response
    {
        $entry->load('collection');
        $blueprint = $entry->blueprint()->with('fields')->firstOrFail();

        return Inertia::render('Content/Entries/Form', [
            'collection' => $entry->collection,
            'blueprint' => $blueprint,
            'entry' => $entry,
            'supportedLocales' => ['tg', 'ru', 'en'],
        ]);
    }

    public function update(EntryRequest $request, Entry $entry): RedirectResponse
    {
        $this->engine->updateEntry($entry, $request->toUpdateData($request->user()?->id));

        return redirect()->route('admin.content.collections.entries.index', $entry->collection_id)
            ->with('success', 'Entry updated successfully.');
    }

    public function destroy(Entry $entry): RedirectResponse
    {
        $collectionId = $entry->collection_id;
        $entry->delete();

        return redirect()->route('admin.content.collections.entries.index', $collectionId)
            ->with('success', 'Entry deleted successfully.');
    }
}
