<?php

namespace App\Http\Controllers\Admin\Schema;

use App\Core\Contracts\Schema\SchemaEngineInterface;
use App\Core\Models\Collection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Schema\CollectionRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    public function __construct(private readonly SchemaEngineInterface $engine) {}

    public function index(): Response
    {
        return Inertia::render('Schema/Collections/Index', [
            'collections' => Collection::withCount('blueprints')->latest()->get(),
        ]);
    }

    public function create(): void
    {
        // Typically handled via modal in Index
    }

    public function store(CollectionRequest $request): RedirectResponse
    {
        $this->engine->createCollection($request->toData());

        return back()->with('success', 'Collection created successfully.');
    }

    public function show(Collection $collection): Response
    {
        $collection->load('blueprints.fields');

        return Inertia::render('Schema/Collections/Show', [
            'collection' => $collection,
        ]);
    }

    public function edit(Collection $collection): void
    {
        // Handled via modal
    }

    public function update(CollectionRequest $request, Collection $collection): RedirectResponse
    {
        $collection->update($request->validated());

        return back()->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $collection->delete();

        return redirect()->route('admin.schema.collections.index')->with('success', 'Collection deleted successfully.');
    }
}
