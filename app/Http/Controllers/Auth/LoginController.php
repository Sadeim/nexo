<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect user after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // protected function guard()
    // {
    //     return Auth::guard('user');
    // }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            if (Auth::user()->status == 0) {
                Auth::logout();

                Session::put('otp_user_email', $request->email);

                return response()->json([
                    'success' => false,
                    'otp_required' => true,
                    'message' => 'check your email for the OTP',
                    'redirect_to' => route('verify_otp_form')
                ]);
            }

            // الكود الخاص بسلة الشراء (إن وجد) ينفذ في حال كان المستخدم مفعل
            $redirectTo = $request->input('redirect_to', url('/'));

            // البحث عن السلة الحالية أو إنشاؤها
            $cookieCart = json_decode(Cookie::get('cart_s', '[]'), true);

            if (isset($cookieCart) && !empty($cookieCart)) {
                $cart = Cart::firstOrCreate(['user_id' => Auth::user()->id]);
                foreach ($cookieCart as $item) {
                    $productId = $item['product_id'];
                    if (!in_array($productId, $cart->products->pluck('id')->toArray())) {
                        $cart->addProductToCart($cart, $productId, $item['quantity']);
                    }
                }
                // إعادة تحميل العلاقة بعد إضافة جميع المنتجات
                $cart->load('products');
                $cart->update([
                    'total_price' => $cart->products->sum('pivot.total_price')
                ]);
            }

            return response()->json([
                'success' => true,
                'redirect_to' => $redirectTo,
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => ['email' => 'بيانات الدخول غير صحيحة']
        ]);
    }


    protected function credentials(Request $request)
    {
        return $request->only('phone_code', 'phone', 'password');
    }

    protected function validateLogin(Request $request)
    {
        return $request->validate([
            'phone_code' => 'nullable',
            'phone' => 'required',
            'password' => 'required',
            'mac_id' => 'required',
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        // return $this->response_api(false, __('auth.admin.failed'),200);
        return redirect()->back()->withErrors([__('auth.admin.failed')]);
    }

    public function username()
    {
        return ['phone_code', 'phone'];
    }

    protected function attemptLogin(Request $request)
    {
        return Auth::guard('user')->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    public function logout(Request $request)
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('home');
    }
}
