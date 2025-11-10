<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedRoles = collect($roles)
            ->flatMap(function ($role) {
                return explode('|', (string) $role);
            })
            ->map(fn ($role) => trim($role))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($allowedRoles)) {
            $allowedRoles = ['user'];
        }

        if (!in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
