<?php

declare(strict_types=1);

namespace App\Modules\Settings\Models\Scopes;

use App\Modules\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Restricts the engine Setting model to the rows it owns on the shared
 * `settings` table.
 *
 * Engine keys are always namespaced as "{group}.{key}" (so they contain a dot),
 * whereas the legacy App\Models\Setting singletons use bare keys (president,
 * site_stats, forum_stats, map_stats). Scoping engine queries to dotted keys
 * keeps the two coexisting key-spaces disjoint: the engine never reads, exports,
 * overwrites or deletes a legacy singleton, and legacy writes never pollute the
 * engine's value map.
 *
 * @implements Scope<Setting>
 */
final class EngineOwnedScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->qualifyColumn('key'), 'like', '%.%');
    }
}
