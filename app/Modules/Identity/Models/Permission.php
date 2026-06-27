<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * KHF permission model — extends Spatie's permission with a human description
 * and a category for grouping in the canonical catalogue.
 *
 * @property string|null $description
 * @property string|null $category
 */
class Permission extends SpatiePermission {}
