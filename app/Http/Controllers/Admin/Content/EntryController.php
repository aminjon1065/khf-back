<?php

namespace App\Http\Controllers\Admin\Content;

use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EntryController extends Controller
{
    public function index(Collection $collection)
    {
        $entries = $collection->entries()->with('author')->latest()->paginate(20);

        return Inertia::render('Content/Entries/Index', [
            'collection' => $collection,
            'entries' => $entries,
        ]);
    }

    public function create(Collection $collection)
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

    public function store(Request $request, Collection $collection)
    {
        $blueprint = Blueprint::with('fields')->findOrFail($request->input('blueprint_id'));

        // Basic validation
        $request->validate([
            'status' => 'required|in:draft,published',
            'data' => 'required|array',
        ]);

        $entry = new Entry;
        $entry->collection_id = $collection->id;
        $entry->blueprint_id = $blueprint->id;
        $entry->author_id = auth()->id();
        $entry->status = $request->input('status');
        $entry->data = $request->input('data');

        // Generate a slug based on title if possible, or use UUID
        $title = $request->input('data.tg.title') ?? $request->input('data.global.title') ?? 'entry-'.Str::random(6);
        $entry->slug = Str::slug($title).'-'.Str::random(4);

        if ($entry->status === 'published') {
            $entry->published_at = now();
        }

        $entry->save();

        return redirect()->route('admin.content.collections.entries.index', $collection)
            ->with('success', 'Entry created successfully.');
    }

    public function edit(Entry $entry)
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

    public function update(Request $request, Entry $entry)
    {
        $request->validate([
            'status' => 'required|in:draft,published',
            'data' => 'required|array',
        ]);

        $entry->status = $request->input('status');
        $entry->data = $request->input('data');

        if ($entry->status === 'published' && ! $entry->published_at) {
            $entry->published_at = now();
        }

        $entry->save();

        return redirect()->route('admin.content.collections.entries.index', $entry->collection_id)
            ->with('success', 'Entry updated successfully.');
    }

    public function destroy(Entry $entry)
    {
        $collectionId = $entry->collection_id;
        $entry->delete();

        return redirect()->route('admin.content.collections.entries.index', $collectionId)
            ->with('success', 'Entry deleted successfully.');
    }
}
