<?php

namespace App\Http\Middleware;

use App\Modules\Identity\Authorization\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coarse admin-shell gate for the staff roles (legacy admin/editor + the new
 * Super Admin). Fine-grained authorization (settings/users/roles and every
 * protected resource) is permission-based via policies + permission-middleware.
 */
class EnsureCanAccessAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->hasAnyRole(['admin', 'editor', Roles::SUPER_ADMIN])) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
