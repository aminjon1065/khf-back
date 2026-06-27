<?php

declare(strict_types=1);

namespace App\Modules\Identity\Http\Resources;

use App\Models\User;
use App\Modules\Media\Contracts\UrlGeneratorInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status->value,
            'locale' => $user->locale,
            'timezone' => $user->timezone,
            'email_verified' => $user->hasVerifiedEmail(),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'roles' => $user->getRoleNames()->values()->all(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
            'avatar_url' => $this->whenLoaded('avatar', fn (): ?string => $user->avatar !== null
                ? app(UrlGeneratorInterface::class)->url($user->avatar)
                : null),
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}
