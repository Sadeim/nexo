<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for all POS screens/actions.
 *
 * Requires an authenticated cashier on the dedicated `pos` guard AND the
 * `pos.access` permission. Unauthenticated users are redirected to the POS
 * login; authenticated-but-unauthorized users get a 403.
 */
class PosAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('pos')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('pos.login');
        }

        if (!Auth::guard('pos')->user()->can('pos.access')) {
            abort(403, 'You do not have access to the POS.');
        }

        return $next($request);
    }
}
