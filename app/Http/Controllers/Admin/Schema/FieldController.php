<?php

namespace App\Http\Controllers\Admin\Schema;

use App\Core\Models\Blueprint;
use App\Core\Models\Field;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Blueprint $blueprint)
    {
        return redirect()->route('admin.schema.blueprints.show', $blueprint);
    }

    public function store(Request $request, Blueprint $blueprint)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'required|string|max:255|unique:fields,handle,NULL,id,blueprint_id,'.$blueprint->id,
            'type' => 'required|string|in:text,textarea,boolean,relation,media',
            'is_translatable' => 'boolean',
            'validation_rules' => 'nullable|array',
            'settings' => 'nullable|array',
            'order' => 'integer',
        ]);

        $blueprint->fields()->create($validated);

        return back()->with('success', 'Field added successfully.');
    }

    public function update(Request $request, Field $field)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'required|string|max:255|unique:fields,handle,'.$field->id.',id,blueprint_id,'.$field->blueprint_id,
            'type' => 'required|string|in:text,textarea,boolean,relation,media',
            'is_translatable' => 'boolean',
            'validation_rules' => 'nullable|array',
            'settings' => 'nullable|array',
            'order' => 'integer',
        ]);

        $field->update($validated);

        return back()->with('success', 'Field updated successfully.');
    }

    public function destroy(Field $field)
    {
        $field->delete();

        return back()->with('success', 'Field removed successfully.');
    }
}
