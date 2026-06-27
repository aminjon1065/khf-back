<?php

namespace App\Http\Controllers\Admin\Schema;

use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlueprintController extends Controller
{
    public function index(Collection $collection)
    {
        return redirect()->route('admin.schema.collections.show', $collection);
    }

    public function create(Collection $collection)
    {
        // Handled via modal
    }

    public function store(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $collection->blueprints()->create($validated);

        return back()->with('success', 'Blueprint created successfully.');
    }

    public function show(Blueprint $blueprint)
    {
        $blueprint->load(['collection', 'fields']);

        return Inertia::render('Schema/Blueprints/Show', [
            'blueprint' => $blueprint,
        ]);
    }

    public function edit(Blueprint $blueprint)
    {
        // Handled via modal
    }

    public function update(Request $request, Blueprint $blueprint)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $blueprint->update($validated);

        return back()->with('success', 'Blueprint updated successfully.');
    }

    public function destroy(Blueprint $blueprint)
    {
        $blueprint->delete();

        return back()->with('success', 'Blueprint deleted successfully.');
    }
}
