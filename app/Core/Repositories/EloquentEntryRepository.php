<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\Schema\EntryRepositoryInterface;
use App\Core\DTO\Schema\CreateEntryData;
use App\Core\DTO\Schema\UpdateEntryData;
use App\Core\Models\Entry;

final class EloquentEntryRepository implements EntryRepositoryInterface
{
    public function find(string $id): ?Entry
    {
        return Entry::find($id);
    }

    public function findOrFail(string $id): Entry
    {
        return Entry::findOrFail($id);
    }

    public function create(CreateEntryData $data): Entry
    {
        $entry = new Entry;
        $entry->collection_id = $data->collectionId;
        $entry->blueprint_id = $data->blueprintId;
        $entry->author_id = $data->authorId;
        $entry->updated_by = $data->authorId;
        $entry->status = $data->status;
        $entry->slug = $data->slug;
        $entry->data = $data->data;
        $entry->version = 1;
        $entry->published_at = $data->publishedAt;
        $entry->save();

        return $entry;
    }

    public function update(Entry $entry, UpdateEntryData $data): Entry
    {
        if ($data->data !== null) {
            $entry->data = $data->data;
        }

        if ($data->slug !== null) {
            $entry->slug = $data->slug;
        }

        if ($data->status !== null) {
            $entry->status = $data->status;
        }

        if ($data->updatedBy !== null) {
            $entry->updated_by = $data->updatedBy;
        }

        $entry->version = $entry->version + 1;
        $entry->save();

        return $entry;
    }

    public function save(Entry $entry): Entry
    {
        $entry->save();

        return $entry;
    }

    public function slugExists(string $slug): bool
    {
        // The slug column carries a DB-level unique index spanning ALL rows,
        // so soft-deleted entries must be included — otherwise a freed-looking
        // slug would still collide at insert time.
        return Entry::withTrashed()->where('slug', $slug)->exists();
    }
}
