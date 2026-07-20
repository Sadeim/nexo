<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Standalone POS login (username + password) on the `pos` guard.
 * Deliberately independent of the admin login — the admin flow is untouched.
 */
class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('pos')->check()) {
            return redirect()->route('pos.index');
        }

        return view('pos.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $ok = Auth::guard('pos')->attempt([
            'email'    => $credentials['email'],
            'password' => $credentials['password'],
            // Only active accounts may sign in.
            'status'   => 1,
        ], $request->boolean('remember'));

        if (!$ok) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Authorization check: must be allowed to use the POS.
        if (!Auth::guard('pos')->user()->can('pos.access')) {
            Auth::guard('pos')->logout();
            throw ValidationException::withMessages([
                'email' => 'This account is not allowed to use the POS.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('pos.index'));
    }

    public function logout(Request $request)
    {
        Auth::guard('pos')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pos.login');
    }
}
