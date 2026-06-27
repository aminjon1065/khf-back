<?php

namespace App\Http\Controllers\Admin\Schema;

use App\Core\Contracts\Schema\SchemaEngineInterface;
use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use App\Http\Controllers\Controller;
use App\Http\Requests\Schema\FieldRequest;
use Illuminate\Http\RedirectResponse;

class FieldController extends Controller
{
    public function __construct(private readonly SchemaEngineInterface $engine) {}

    public function index(Blueprint $blueprint): RedirectResponse
    {
        return redirect()->route('admin.schema.blueprints.show', $blueprint);
    }

    public function store(FieldRequest $request, Blueprint $blueprint): RedirectResponse
    {
        $this->engine->addField($request->toData());

        return back()->with('success', 'Field added successfully.');
    }

    public function update(FieldRequest $request, BlueprintField $field): RedirectResponse
    {
        $field->update($request->validated());

        return back()->with('success', 'Field updated successfully.');
    }

    public function destroy(BlueprintField $field): RedirectResponse
    {
        $field->delete();

        return back()->with('success', 'Field removed successfully.');
    }
}
