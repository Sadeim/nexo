<?php

namespace App\Http\Middleware;

use App\Support\Portal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Holds a barber on the change-password screen until they have chosen one.
 *
 * The owner types the first password, which means that until it is changed it
 * is a credential two people know. Everything behind the portal is this
 * barber's own schedule, so "set it later" is not good enough — the forced
 * change is the only moment where the password becomes theirs alone.
 *
 * Applied to the screens, not to the change-password route itself, so the one
 * page they are allowed to reach stays reachable.
 */
class EnsureEmployeePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $employee = Auth::guard('employee')->user();

        if ($employee && $employee->must_change_password) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Password change required.'], 403);
            }

            return redirect()->to(Portal::route('password.edit'));
        }

        return $next($request);
    }
}
