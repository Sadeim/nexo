<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for every portal screen.
 *
 * Two jobs, and the second is the one that matters: a barber who has left is
 * deactivated in the dashboard, not deleted, because their sales history has
 * to stay attached to them. Checking is_active on the session alone would let
 * a barber keep browsing the portal until their session expired — up to twelve
 * hours after being switched off. Checking it on every request closes that.
 */
class EmployeeAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('employee');

        if (! $guard->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('portal.login'));
        }

        if (! $guard->user()->is_active) {
            // Ending the session first means the 403 is the last thing that
            // account can do, rather than something it can retry by reloading.
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'This account is no longer active.');
        }

        return $next($request);
    }
}
