<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Пропускает только запросы фронтенда (Next.js), которые серверно шлют
 * общий токен. Без валидного токена API закрыт (fail-closed).
 */
class EnsureFrontendRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('khf.frontend_api_token');
        $provided = $this->token($request);

        if ($expected === '' || $provided === null || ! hash_equals($expected, $provided)) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized.');
        }

        return $next($request);
    }

    private function token(Request $request): ?string
    {
        $bearer = $request->bearerToken();
        if ($bearer !== null && $bearer !== '') {
            return $bearer;
        }

        $header = $request->header('X-Frontend-Token');

        return ($header !== null && $header !== '') ? (string) $header : null;
    }
}
