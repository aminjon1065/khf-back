<?php

declare(strict_types=1);

namespace App\Modules\Identity\Listeners;

use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use Illuminate\Auth\Events\Failed;

final class HandleFailedLogin
{
    public function __construct(private readonly ActivityLoggerInterface $activity) {}

    public function handle(Failed $event): void
    {
        $email = is_string($event->credentials['email'] ?? null) ? $event->credentials['email'] : null;

        $this->activity->record('auth.failed', null, 'Failed login attempt', ['email' => $email]);
    }
}
