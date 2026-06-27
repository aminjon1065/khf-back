<?php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Marker interface for CMS use-case actions.
 *
 * Each concrete action defines its own typed handle() method.
 * The interface enables tagging in the DI container and type-hinting
 * in callers that accept "any action" without knowing its signature.
 */
interface ActionInterface {}
