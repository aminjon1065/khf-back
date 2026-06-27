<?php

declare(strict_types=1);

namespace App\Core\Actions\Schema;

use App\Core\Actions\Action;
use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\Schema\EntryRepositoryInterface;
use App\Core\DTO\Schema\UpdateEntryData;
use App\Core\Enums\EntryStatus;
use App\Core\Events\Schema\EntryArchived;
use App\Core\Events\Schema\EntryPublished;
use App\Core\Models\Entry;
use App\Core\Schema\SchemaHooks;

/**
 * Apply a partial update to an entry. Status transitions made through this
 * action emit the same domain events as the dedicated publish/archive actions,
 * so observers see a single, consistent lifecycle regardless of entry path.
 */
final class UpdateEntryAction extends Action
{
    public function __construct(
        private readonly EntryRepositoryInterface $entries,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(Entry $entry, UpdateEntryData $data): Entry
    {
        $this->hooks->doAction(SchemaHooks::ENTRY_UPDATING, $entry, $data);

        $previousStatus = $entry->status;
        $targetStatus = $data->status ?? $previousStatus;

        if ($targetStatus === EntryStatus::Published && $entry->published_at === null) {
            $entry->published_at = now();
        }

        $entry = $this->entries->update($entry, $data);

        $this->hooks->doAction(SchemaHooks::ENTRY_UPDATED, $entry);

        if ($previousStatus !== EntryStatus::Published && $targetStatus === EntryStatus::Published) {
            $this->hooks->doAction(SchemaHooks::ENTRY_PUBLISHED, $entry);
            $this->events->dispatch(new EntryPublished($entry));
        }

        if ($previousStatus !== EntryStatus::Archived && $targetStatus === EntryStatus::Archived) {
            $this->hooks->doAction(SchemaHooks::ENTRY_ARCHIVED, $entry);
            $this->events->dispatch(new EntryArchived($entry));
        }

        return $entry;
    }
}
