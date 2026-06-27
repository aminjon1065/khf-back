<?php

namespace App\Http\Controllers\Admin\Schema;

use App\Core\Models\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollectionController extends Controller
{
    public function index()
    {
        return Inertia::render('Schema/Collections/Index', [
            'collections' => Collection::withCount('blueprints')->latest()->get(),
        ]);
    }

    public function create()
    {
        // Typically handled via modal in Index
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:collections,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        Collection::create($validated);

        return back()->with('success', 'Collection created successfully.');
    }

    public function show(Collection $collection)
    {
        $collection->load('blueprints.fields');

        return Inertia::render('Schema/Collections/Show', [
            'collection' => $collection,
        ]);
    }

    public function edit(Collection $collection)
    {
        // Handled via modal
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:collections,slug,'.$collection->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        $collection->update($validated);

        return back()->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()->route('admin.schema.collections.index')->with('success', 'Collection deleted successfully.');
    }
}
