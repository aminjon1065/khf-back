<?php

declare(strict_types=1);

namespace App\Modules\Media\Exceptions;

use RuntimeException;

/**
 * Base exception for the Media Engine. All module failures derive from this so
 * callers can catch the whole subsystem with a single type.
 */
class MediaException extends RuntimeException {}
