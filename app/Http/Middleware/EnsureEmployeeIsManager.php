<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The gate in front of All Bookings.
 *
 * Every other portal screen shows the signed-in employee their own work and
 * needs no such check — there is nothing to get wrong. This one shows the
 * whole shop, so who may open it is decided here, once, in front of the
 * route, rather than inside a controller that could later grow a second
 * entrance.
 *
 * 403 rather than a redirect: a barber reaching this URL has not lost their
 * way, and bouncing them to Today would say "that worked" about something
 * that did not.
 */
class EnsureEmployeeIsManager
{
    public function handle(Request $request, Closure $next)
    {
        $employee = Auth::guard('employee')->user();

        // isManager() compares against the exact role value, so anything
        // else — an unloaded attribute, a null, a value nobody recognises —
        // reads as "no".
        abort_unless($employee && $employee->isManager(), 403);

        return $next($request);
    }
}
