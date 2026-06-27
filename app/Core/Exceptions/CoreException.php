<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use RuntimeException;

/**
 * Root exception for all KHF CMS platform errors.
 *
 * Callers that want to catch any CMS-originated exception can catch this base class.
 * Callers that need to distinguish failure modes should catch the specific subclasses.
 */
class CoreException extends RuntimeException {}
