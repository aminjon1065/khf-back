<?php

namespace App\Http\Requests\Schema;

use App\Core\DTO\Schema\CreateBlueprintData;
use Illuminate\Foundation\Http\FormRequest;

class BlueprintRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function toData(string $collectionId): CreateBlueprintData
    {
        $validated = $this->validated();

        return new CreateBlueprintData(
            collectionId: $collectionId,
            name: (string) $validated['name'],
        );
    }
}
