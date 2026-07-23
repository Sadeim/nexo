<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dashboard authorization gate.
 *
 * The `admin` session guard is shared by every admins-table account, including
 * cashiers. Authentication alone is therefore NOT enough to enter the
 * dashboard: this middleware additionally requires the `admin` role, so a
 * cashier who authenticates through the admin login form still cannot reach any
 * dashboard page (they get a 403).
 *
 * Cashiers using the POS log in on the separate `pos` guard and never hold an
 * `admin` guard session at all, so they are blocked here too.
 */
class EnsureAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        if (!Auth::guard('admin')->user()->hasRole('admin')) {
            abort(403, 'This account is not allowed to access the dashboard.');
        }

        return $next($request);
    }
}
