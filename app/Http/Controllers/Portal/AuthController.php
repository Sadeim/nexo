<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Support\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Sign-in and password management for the barbers' portal.
 *
 * Phone plus password on the `employee` guard. Deliberately separate from both
 * the admin and POS logins: nothing here can grant admin access, and nothing
 * there can grant portal access.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->to(Portal::route('today'));
        }

        return view('portal.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:32'],
            'password' => ['required', 'string'],
        ]);

        // Normalised the same way the dashboard stores it, so the barber can
        // type their number however they think of it.
        $phone = Employee::normalizePhone($data['phone']);

        $employee = $phone ? Employee::withPhone($phone)->first() : null;

        /*
         * One message for every failure: no such phone, wrong password,
         * deactivated account, portal never set up. Saying which would let
         * anyone test whether a given number belongs to a barber here.
         *
         * Hash::check runs only when there is a password to check, so an
         * unconfigured row is rejected without comparing against null.
         */
        $ok = $employee
            && $employee->is_active
            && $employee->hasPortalAccess()
            && Hash::check($data['password'], $employee->password);

        if (! $ok) {
            throw ValidationException::withMessages([
                'phone' => 'Those details do not match an active account.',
            ]);
        }

        Auth::guard('employee')->login($employee);

        // Fresh session id on sign-in, so a session fixed before login is
        // not the session that ends up authenticated.
        $request->session()->regenerate();

        $employee->recordLogin();

        if ($employee->must_change_password) {
            return redirect()->to(Portal::route('password.edit'));
        }

        return redirect()->intended(Portal::route('today'));
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(Portal::route('login'));
    }

    // ---------- password ----------

    public function editPassword()
    {
        return view('portal.password', [
            'forced' => (bool) Auth::guard('employee')->user()->must_change_password,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $employee->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'That is not your current password.',
            ]);
        }

        if (Hash::check($data['password'], $employee->password)) {
            throw ValidationException::withMessages([
                'password' => 'Please choose a password you have not used here before.',
            ]);
        }

        $employee->changePasswordTo($data['password']);

        // The old password may be known to the owner who issued it. Rotating
        // the session id means any other session opened with it is orphaned.
        $request->session()->regenerate();

        return redirect()->to(Portal::route('today'))
            ->with('portal_status', 'Your password has been changed.');
    }
}
