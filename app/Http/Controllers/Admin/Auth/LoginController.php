<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller For Admin
    |--------------------------------------------------------------------------
    */

    use AuthenticatesUsers;


    protected $redirectTo = "admin";
    public $guard_name;

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    }


    protected function attemptLogin(Request $request)
    {
        $response = $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember_token')
        );

        return $response;
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->guard()->user();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return redirect()->route('admin.home');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()->withErrors([ __('auth.admin.failed')]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
