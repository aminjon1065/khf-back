<?php

declare(strict_types=1);

namespace App\Core\Contracts\Schema;

use App\Core\DTO\Schema\CreateBlueprintData;
use App\Core\DTO\Schema\CreateBlueprintFieldData;
use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use App\Core\Models\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface BlueprintRepositoryInterface
{
    public function find(string $id): ?Blueprint;

    public function findOrFail(string $id): Blueprint;

    public function create(CreateBlueprintData $data): Blueprint;

    public function addField(CreateBlueprintFieldData $data): BlueprintField;

    /**
     * @return EloquentCollection<int, Blueprint>
     */
    public function forCollection(Collection $collection): EloquentCollection;
}
