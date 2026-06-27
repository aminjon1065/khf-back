<?php

declare(strict_types=1);

namespace App\Core\Actions;

use App\Core\Contracts\ActionInterface;

/**
 * Base class for all CMS use-case actions.
 *
 * Concrete actions are plain PHP classes with constructor-injected dependencies
 * and a typed handle() method. The static run() factory resolves the action from
 * the container and forwards all arguments to handle(), enabling clean call sites:
 *
 *   CreateEntryAction::run($dto)
 *
 * instead of:
 *
 *   app(CreateEntryAction::class)->handle($dto)
 *
 * Each concrete action defines its own typed handle() signature.
 * Example:
 *
 *   final class CreateEntryAction extends Action {
 *       public function __construct(private readonly EntryRepository $repo) {}
 *
 *       public function handle(CreateEntryDto $dto): Entry {
 *           return $this->repo->create($dto);
 *       }
 *   }
 */
abstract class Action implements ActionInterface
{
    /**
     * Resolve the action from the container and forward arguments to handle().
     */
    public static function run(mixed ...$args): mixed
    {
        return app(static::class)->handle(...$args);
    }
}
