<?php

namespace App\Http\Requests\Schema;

use App\Core\DTO\Schema\CreateCollectionData;
use App\Core\Models\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CollectionRequest extends FormRequest
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
        $collection = $this->route('collection');
        $collectionId = $collection instanceof Collection ? $collection->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('collections', 'slug')->ignore($collectionId)],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toData(): CreateCollectionData
    {
        $validated = $this->validated();

        return new CreateCollectionData(
            name: (string) $validated['name'],
            slug: (string) $validated['slug'],
            description: isset($validated['description']) ? (string) $validated['description'] : null,
            icon: isset($validated['icon']) ? (string) $validated['icon'] : null,
        );
    }
}
