<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\Cart;
use App\Models\Role;
use App\Models\User;
use App\Traits\Rule\CustomValidationRulesTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use CustomValidationRulesTrait;

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // $check_phone = $this->checkPhone('users', $data['phone_code'], $data['phone']);

        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email'     => ['required',  'unique:users,email', 'email:rfc,dns'],
            'phone'         => ['required', 'digits_between:9,15'],
            'phone_code'    => ['nullable', 'numeric'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|email:rfc,dns',
            'phone' => 'required|digits_between:8,15|unique:users,phone',
            'phone_code' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ]);

        $otp = random_int(100000, 999999);
        $otpExpiredAt = now()->addMinutes(10);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'phone_code' => $request->phone_code,
            'password' => Hash::make($request->password),
            'otp'              => $otp,
            'otp_expired_at'   => $otpExpiredAt,
            'status'           => 0,
        ]);

        session(['otp_user_email' => $request->email]);

        Mail::to($user->email)->send(new VerificationCodeMail($user));

        // البحث عن السلة الحالية أو إنشاؤها
        $cookieCart = json_decode(Cookie::get('cart_s', '[]'), true);
    
        if (isset($cookieCart) && !empty($cookieCart)) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            foreach ($cookieCart as $item) {
                $cart->addProductToCart($cart, $item['product_id'], $item['quantity']);
            }
        }

        return response()->json(['success' => true, 'message' => 'account created successfully, check your email for the OTP', 'redirect_to' => route('verify_otp_form')]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'phone_code' => $data['phone_code'],
            'password' => Hash::make($data['password']),
        ]);


        return $user;
    }

    protected function guard()
    {
        return Auth::guard('user');
    }

    public function showOtpForm()
    {
        // $userId = session('otp_user_email');
        // if (!$userId) {
        //     return redirect()->route('login')->with('error', 'Unauthorized access.');
        // }

        return view('frontend.verify_otp');
    }
    
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $userEmail = session('otp_user_email');
        if (!$userEmail) {
            return response()->json([
                'success' => false,
                'message' => 'User session expired. Please login again.'
            ], 401);
        }

        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        if ($user->otp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'The OTP code is incorrect.'
            ]);
        }

        if (Carbon::now()->gt($user->otp_expired_at)) {
            return response()->json([
                'success' => false,
                'message' => 'The OTP code has expired. Please request a new one.'
            ]);
        }

        $user->email_verified_at = Carbon::now();
        $user->status = 1;
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Your account has been verified successfully.'
        ]);
    }
 
    public function resendOTP(Request $request)
    {
        // استرجاع البريد الإلكتروني من الجلسة
        $email = session('otp_user_email');
        if (!$email) {
            return redirect()->back()->withErrors(['email' => 'لا يوجد بريد إلكتروني لتأكيده']);
        }
    
        // تطبيق تحديد معدل الطلب (Rate Limiting) لتجنب الاستغلال
        $key = 'otp_resend_' . $email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return redirect()->back()->withErrors([
                'rate_limit' => 'exceeded the limit of 5 attempts, please try again later',
            ]);
        }
        // تسجيل محاولة إعادة إرسال OTP مع صلاحية المفتاح لمدة دقيقة
        RateLimiter::hit($key, 60);
    
        // التأكد من وجود المستخدم المرتبط بالبريد الإلكتروني
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'المستخدم غير موجود']);
        }
    
        // توليد رمز OTP آمن مع تحديد وقت انتهاء الصلاحية
        $otp = random_int(100000, 999999);
        $otpExpiredAt = now()->addMinutes(10);
    
        // تحديث بيانات المستخدم برمز OTP ووقت انتهاء الصلاحية
        $user->update([
            'otp'            => $otp,
            'otp_expired_at' => $otpExpiredAt,
        ]);
    
        // تسجيل الحدث لمراقبة محاولات إعادة الإرسال
        Log::info("OTP resent for user: {$user->email}");
    
        // إرسال رمز OTP إلى البريد الإلكتروني للمستخدم
        Mail::to($user->email)->send(new VerificationCodeMail($user));
    
        return redirect()->back()->with('status', 'otp resent');
    }
    
}
