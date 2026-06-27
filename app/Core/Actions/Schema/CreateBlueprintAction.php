<?php

declare(strict_types=1);

namespace App\Core\Actions\Schema;

use App\Core\Actions\Action;
use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\Schema\BlueprintRepositoryInterface;
use App\Core\DTO\Schema\CreateBlueprintData;
use App\Core\Events\Schema\BlueprintCreated;
use App\Core\Models\Blueprint;
use App\Core\Schema\SchemaHooks;

final class CreateBlueprintAction extends Action
{
    public function __construct(
        private readonly BlueprintRepositoryInterface $blueprints,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(CreateBlueprintData $data): Blueprint
    {
        $blueprint = $this->blueprints->create($data);

        $this->hooks->doAction(SchemaHooks::BLUEPRINT_CREATED, $blueprint);
        $this->events->dispatch(new BlueprintCreated($blueprint));

        return $blueprint;
    }
}
