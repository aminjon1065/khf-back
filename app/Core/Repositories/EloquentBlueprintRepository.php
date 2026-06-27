<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\Schema\BlueprintRepositoryInterface;
use App\Core\DTO\Schema\CreateBlueprintData;
use App\Core\DTO\Schema\CreateBlueprintFieldData;
use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use App\Core\Models\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

final class EloquentBlueprintRepository implements BlueprintRepositoryInterface
{
    public function find(string $id): ?Blueprint
    {
        return Blueprint::find($id);
    }

    public function findOrFail(string $id): Blueprint
    {
        return Blueprint::findOrFail($id);
    }

    public function create(CreateBlueprintData $data): Blueprint
    {
        return Blueprint::create([
            'collection_id' => $data->collectionId,
            'name' => $data->name,
        ]);
    }

    public function addField(CreateBlueprintFieldData $data): BlueprintField
    {
        return BlueprintField::create([
            'blueprint_id' => $data->blueprintId,
            'name' => $data->name,
            'handle' => $data->handle,
            'type' => $data->type,
            'is_translatable' => $data->isTranslatable,
            'validation_rules' => $data->validationRules,
            'settings' => $data->settings,
            'order' => $data->order,
        ]);
    }

    /**
     * @return EloquentCollection<int, Blueprint>
     */
    public function forCollection(Collection $collection): EloquentCollection
    {
        return $collection->blueprints()->with('fields')->get();
    }
}
