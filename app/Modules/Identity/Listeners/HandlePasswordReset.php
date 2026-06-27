<?php

declare(strict_types=1);

namespace App\Modules\Identity\Listeners;

use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use Illuminate\Auth\Events\PasswordReset;

final class HandlePasswordReset
{
    public function __construct(private readonly ActivityLoggerInterface $activity) {}

    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        $this->activity->record('auth.password_reset', $user, 'Password reset');
    }
}
