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
        \Log::info('role-middleware', [
    'required' => $roles ?? null,          // middleware引数
    'actual_code' => auth()->user()?->role?->role_code,
    'actual_name' => auth()->user()?->role?->role_name,
]);
        if(!auth()->check()) {
            return redirect()->route('login');
        }

        if(!in_array(auth()->user()->role->role_code, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
