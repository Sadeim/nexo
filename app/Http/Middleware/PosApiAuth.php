<?php

namespace App\Http\Middleware;

use App\Models\PosApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bearer-token gate for the Flutter POS JSON API.
 * (The browser-based Web POS uses `PosAuthenticate` instead — session auth.)
 */
class PosApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $token = PosApiToken::with('admin')->where('token', $bearer)->first();

        if (!$token || !$token->admin) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        $request->attributes->set('pos_admin', $token->admin);
        $request->attributes->set('pos_token', $token);

        return $next($request);
    }
}
