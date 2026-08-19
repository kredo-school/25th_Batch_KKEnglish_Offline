<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request, ...$roles): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if(!auth()->check()) {
            return redirect()->route('login');
        }

        if(!in_array(auth()->user()->role->role_code, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
