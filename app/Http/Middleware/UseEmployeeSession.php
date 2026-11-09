<?php

namespace App\Http\Middleware;

use App\Support\Portal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives the portal its own session cookie, shared by both of its mounts.
 *
 * Laravel keeps one session per browser per cookie name, and all guards share
 * it. With a single cookie, the owner signing in to a barber's portal to check
 * something would be writing into the same session row that holds their admin
 * login — and `session()->invalidate()` on portal logout would end both.
 *
 * Pointing /me at a differently named cookie gives it a genuinely separate
 * session: separate row, separate CSRF token, separate lifetime. An admin
 * session and an employee session can then coexist, and neither logout
 * touches the other.
 *
 * This must run BEFORE StartSession, which is why it is prepended to the web
 * group rather than attached to the routes. By the time a route's own
 * middleware runs, the session has already been opened under the old name.
 */
class UseEmployeeSession
{
    /** Distinct from the app's default session cookie. */
    public const COOKIE = 'nexo_employee_session';

    public function handle(Request $request, Closure $next): Response
    {
        // Both mounts of the portal, so /me and /work share one session:
        // signing in on a phone signs you in at the desk and vice versa.
        if ($request->is(...Portal::pathPatterns())) {
            config(['session.cookie' => self::COOKIE]);
        }

        return $next($request);
    }
}
