<?php

declare(strict_types=1);

namespace App\Core\Contracts\Schema;

use App\Core\DTO\Schema\CreateEntryData;
use App\Core\DTO\Schema\UpdateEntryData;
use App\Core\Models\Entry;

interface EntryRepositoryInterface
{
    public function find(string $id): ?Entry;

    public function findOrFail(string $id): Entry;

    public function create(CreateEntryData $data): Entry;

    public function update(Entry $entry, UpdateEntryData $data): Entry;

    public function save(Entry $entry): Entry;

    public function slugExists(string $slug): bool;
}
