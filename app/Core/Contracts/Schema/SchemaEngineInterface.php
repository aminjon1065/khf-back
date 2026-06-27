<?php

declare(strict_types=1);

namespace App\Core\Contracts\Schema;

use App\Core\DTO\Schema\CreateBlueprintData;
use App\Core\DTO\Schema\CreateBlueprintFieldData;
use App\Core\DTO\Schema\CreateCollectionData;
use App\Core\DTO\Schema\CreateEntryData;
use App\Core\DTO\Schema\UpdateEntryData;
use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use App\Core\Models\Collection;
use App\Core\Models\Entry;

/**
 * Canonical entry point to the KHF content model.
 *
 * Every future module consumes the Schema Engine through this contract. The
 * engine itself depends only on Core abstractions (repositories, events, hooks)
 * and never on any future module.
 */
interface SchemaEngineInterface
{
    public function createCollection(CreateCollectionData $data): Collection;

    public function createBlueprint(CreateBlueprintData $data): Blueprint;

    public function addField(CreateBlueprintFieldData $data): BlueprintField;

    public function createEntry(CreateEntryData $data): Entry;

    public function updateEntry(Entry $entry, UpdateEntryData $data): Entry;

    public function publishEntry(Entry $entry, ?int $actorId = null): Entry;

    public function archiveEntry(Entry $entry, ?int $actorId = null): Entry;

    public function fieldTypes(): FieldTypeRegistryInterface;
}
