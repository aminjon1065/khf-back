<?php

declare(strict_types=1);

namespace App\Core\Actions\Schema;

use App\Core\Actions\Action;
use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Core\DTO\Schema\CreateCollectionData;
use App\Core\Events\Schema\CollectionCreated;
use App\Core\Models\Collection;
use App\Core\Schema\SchemaHooks;

/**
 * Create a collection — the top-level content container. Collection creation is
 * a trivial aggregate-root write, so it uses the model directly rather than a
 * dedicated repository; Blueprint and Entry warrant repositories for their
 * richer query surface.
 */
final class CreateCollectionAction extends Action
{
    public function __construct(
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(CreateCollectionData $data): Collection
    {
        $collection = Collection::create([
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'icon' => $data->icon,
        ]);

        $this->hooks->doAction(SchemaHooks::COLLECTION_CREATED, $collection);
        $this->events->dispatch(new CollectionCreated($collection));

        return $collection;
    }
}
