<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives the portal its own session cookie.
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
        if ($request->is('me', 'me/*')) {
            config(['session.cookie' => self::COOKIE]);
        }

        return $next($request);
    }
}
