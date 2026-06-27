<?php

namespace App\Http\Controllers\Admin\Schema;

use App\Core\Contracts\Schema\SchemaEngineInterface;
use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Schema\BlueprintRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlueprintController extends Controller
{
    public function __construct(private readonly SchemaEngineInterface $engine) {}

    public function index(Collection $collection): RedirectResponse
    {
        return redirect()->route('admin.schema.collections.show', $collection);
    }

    public function create(Collection $collection): void
    {
        // Handled via modal
    }

    public function store(BlueprintRequest $request, Collection $collection): RedirectResponse
    {
        $this->engine->createBlueprint($request->toData($collection->id));

        return back()->with('success', 'Blueprint created successfully.');
    }

    public function show(Blueprint $blueprint): Response
    {
        $blueprint->load(['collection', 'fields']);

        return Inertia::render('Schema/Blueprints/Show', [
            'blueprint' => $blueprint,
        ]);
    }

    public function edit(Blueprint $blueprint): void
    {
        // Handled via modal
    }

    public function update(BlueprintRequest $request, Blueprint $blueprint): RedirectResponse
    {
        $blueprint->update($request->validated());

        return back()->with('success', 'Blueprint updated successfully.');
    }

    public function destroy(Blueprint $blueprint): RedirectResponse
    {
        $blueprint->delete();

        return back()->with('success', 'Blueprint deleted successfully.');
    }
}
