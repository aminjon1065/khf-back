<?php

declare(strict_types=1);

namespace App\Core\Actions\Schema;

use App\Core\Actions\Action;
use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\Schema\EntryRepositoryInterface;
use App\Core\DTO\Schema\CreateEntryData;
use App\Core\Enums\EntryStatus;
use App\Core\Events\Schema\EntryCreated;
use App\Core\Events\Schema\EntryPublished;
use App\Core\Models\Entry;
use App\Core\Schema\SchemaHooks;
use Illuminate\Support\Str;

final class CreateEntryAction extends Action
{
    public function __construct(
        private readonly EntryRepositoryInterface $entries,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(CreateEntryData $data): Entry
    {
        $this->hooks->doAction(SchemaHooks::ENTRY_CREATING, $data);

        $slug = $this->resolveUniqueSlug($data->slug);
        $publishedAt = $data->status === EntryStatus::Published ? now() : null;

        $entry = $this->entries->create(
            $data->withSlug($slug)->withPublishedAt($publishedAt),
        );

        $this->hooks->doAction(SchemaHooks::ENTRY_CREATED, $entry);
        $this->events->dispatch(new EntryCreated($entry));

        if ($entry->status === EntryStatus::Published) {
            $this->hooks->doAction(SchemaHooks::ENTRY_PUBLISHED, $entry);
            $this->events->dispatch(new EntryPublished($entry));
        }

        return $entry;
    }

    /**
     * Slugify the candidate (falling back to "entry") and guarantee global
     * uniqueness by appending an incrementing suffix.
     */
    private function resolveUniqueSlug(?string $candidate): string
    {
        $base = Str::slug((string) $candidate);

        if ($base === '') {
            $base = 'entry';
        }

        $slug = $base;
        $suffix = 1;

        while ($this->entries->slugExists($slug)) {
            $suffix++;
            $slug = "{$base}-{$suffix}";
        }

        return $slug;
    }
}
