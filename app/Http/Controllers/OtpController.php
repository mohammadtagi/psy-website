<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\MobileNormalizationService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otp,
        protected MobileNormalizationService $normalizer,
    ) {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function send(SendOtpRequest $request)
    {
        try {
            $this->otp->issue($request->input('mobile'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['mobile' => $e->getMessage()]);
        }

        session(['otp_mobile' => $this->normalizer->normalize($request->input('mobile'))]);

        return redirect()
            ->route('verify')
            ->with('status', 'کد تایید به شماره شما ارسال شد.');
    }

    public function showVerify()
    {
        if (! session('otp_mobile')) {
            return redirect()->route('login');
        }

        return view('auth.verify');
    }

    public function verify(VerifyOtpRequest $request)
    {
        $mobile = session('otp_mobile', $request->input('mobile'));

        try {
            $this->otp->verify($mobile, $request->input('code'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        // ثبت‌نام/ورود خودکار بر اساس شماره موبایل
        $user = User::firstOrCreate(
            ['mobile' => $mobile],
            ['role' => 'client', 'status' => 'active'],
        );

        $user->forceFill(['last_login_at' => now()])->save();

        Auth::login($user, remember: true);
        session()->forget('otp_mobile');
        $request->session()->regenerate();

        if (! $user->name) {
            return redirect()->route('profile.edit')
                ->with('status', 'لطفاً پروفایل خود را تکمیل کنید.');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
