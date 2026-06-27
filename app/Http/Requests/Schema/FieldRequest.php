<?php

namespace App\Http\Requests\Schema;

use App\Core\DTO\Schema\CreateBlueprintFieldData;
use App\Core\Enums\FieldType;
use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // access restricted by permission middleware on the route
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $field = $this->route('field');
        $fieldId = $field instanceof BlueprintField ? $field->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'handle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fields', 'handle')->where('blueprint_id', $this->resolveBlueprintId())->ignore($fieldId),
            ],
            'type' => ['required', Rule::enum(FieldType::class)],
            'is_translatable' => ['boolean'],
            'validation_rules' => ['nullable', 'array'],
            'validation_rules.*' => ['string'],
            'settings' => ['nullable', 'array'],
            'order' => ['integer'],
        ];
    }

    public function toData(): CreateBlueprintFieldData
    {
        $validated = $this->validated();

        $rules = isset($validated['validation_rules'])
            ? array_values(array_map(strval(...), (array) $validated['validation_rules']))
            : null;

        $settings = isset($validated['settings']) ? (array) $validated['settings'] : null;

        return new CreateBlueprintFieldData(
            blueprintId: $this->resolveBlueprintId(),
            name: (string) $validated['name'],
            handle: (string) $validated['handle'],
            type: FieldType::from((string) $validated['type']),
            // Leave null when the flag is absent so the field type's
            // isTranslatableByDefault() resolves it (e.g. Text defaults to true).
            isTranslatable: $this->has('is_translatable') ? $this->boolean('is_translatable') : null,
            validationRules: $rules,
            settings: $settings,
            order: (int) ($validated['order'] ?? 0),
        );
    }

    private function resolveBlueprintId(): string
    {
        $blueprint = $this->route('blueprint');

        if ($blueprint instanceof Blueprint) {
            return $blueprint->id;
        }

        $field = $this->route('field');

        if ($field instanceof BlueprintField) {
            return $field->blueprint_id;
        }

        return '';
    }
}
