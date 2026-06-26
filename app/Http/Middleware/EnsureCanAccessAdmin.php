<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Доступ в админку только для ролей admin/editor.
 * Тонкие права (settings/users/roles) проверяются permission-middleware на роутах.
 */
class EnsureCanAccessAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->hasAnyRole(['admin', 'editor'])) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
