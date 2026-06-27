<?php

declare(strict_types=1);

namespace App\Core\Schema;

use App\Core\Actions\Schema\ArchiveEntryAction;
use App\Core\Actions\Schema\CreateBlueprintAction;
use App\Core\Actions\Schema\CreateBlueprintFieldAction;
use App\Core\Actions\Schema\CreateCollectionAction;
use App\Core\Actions\Schema\CreateEntryAction;
use App\Core\Actions\Schema\PublishEntryAction;
use App\Core\Actions\Schema\UpdateEntryAction;
use App\Core\Contracts\Schema\FieldTypeRegistryInterface;
use App\Core\Contracts\Schema\SchemaEngineInterface;
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
 * Façade over the Schema Engine use-cases.
 *
 * Provides one canonical, dependency-injectable entry point that future modules
 * consume instead of reaching for individual actions or Eloquent models. Each
 * method delegates to the matching Action, which owns the orchestration (events,
 * hooks, persistence). The engine depends only on Core abstractions.
 */
final class SchemaEngine implements SchemaEngineInterface
{
    public function __construct(
        private readonly CreateCollectionAction $createCollectionAction,
        private readonly CreateBlueprintAction $createBlueprintAction,
        private readonly CreateBlueprintFieldAction $createBlueprintFieldAction,
        private readonly CreateEntryAction $createEntryAction,
        private readonly UpdateEntryAction $updateEntryAction,
        private readonly PublishEntryAction $publishEntryAction,
        private readonly ArchiveEntryAction $archiveEntryAction,
        private readonly FieldTypeRegistryInterface $fieldTypes,
    ) {}

    public function createCollection(CreateCollectionData $data): Collection
    {
        return $this->createCollectionAction->handle($data);
    }

    public function createBlueprint(CreateBlueprintData $data): Blueprint
    {
        return $this->createBlueprintAction->handle($data);
    }

    public function addField(CreateBlueprintFieldData $data): BlueprintField
    {
        return $this->createBlueprintFieldAction->handle($data);
    }

    public function createEntry(CreateEntryData $data): Entry
    {
        return $this->createEntryAction->handle($data);
    }

    public function updateEntry(Entry $entry, UpdateEntryData $data): Entry
    {
        return $this->updateEntryAction->handle($entry, $data);
    }

    public function publishEntry(Entry $entry, ?int $actorId = null): Entry
    {
        return $this->publishEntryAction->handle($entry, $actorId);
    }

    public function archiveEntry(Entry $entry, ?int $actorId = null): Entry
    {
        return $this->archiveEntryAction->handle($entry, $actorId);
    }

    public function fieldTypes(): FieldTypeRegistryInterface
    {
        return $this->fieldTypes;
    }
}
