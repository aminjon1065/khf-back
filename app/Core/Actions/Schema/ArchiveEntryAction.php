<?php

declare(strict_types=1);

namespace App\Core\Actions\Schema;

use App\Core\Actions\Action;
use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\Schema\EntryRepositoryInterface;
use App\Core\Enums\EntryStatus;
use App\Core\Events\Schema\EntryArchived;
use App\Core\Models\Entry;
use App\Core\Schema\SchemaHooks;

final class ArchiveEntryAction extends Action
{
    public function __construct(
        private readonly EntryRepositoryInterface $entries,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(Entry $entry, ?int $actorId = null): Entry
    {
        if ($entry->status === EntryStatus::Archived) {
            return $entry;
        }

        $entry->status = EntryStatus::Archived;

        if ($actorId !== null) {
            $entry->updated_by = $actorId;
        }

        $entry->version = $entry->version + 1;
        $this->entries->save($entry);

        $this->hooks->doAction(SchemaHooks::ENTRY_ARCHIVED, $entry);
        $this->events->dispatch(new EntryArchived($entry));

        return $entry;
    }
}
