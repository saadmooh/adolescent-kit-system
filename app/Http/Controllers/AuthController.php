<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('portal.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // مفتاح تتبع محاولات الدخول بحسب البريد وعنوان الـ IP
        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // حماية من هجمات القوة الغاشمة والتخمين (5 محاولات في الدقيقة)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "تم حظر المحاولات مؤقتاً لتكرار الخطأ. يرجى الانتظار {$seconds} ثانية قبل المحاولة مرة أخرى.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', "مرحباً بك يا مدير النظام: {$user->name}");
            }

            return redirect()->intended(route('portal.dashboard'))
                ->with('success', "أهلاً بك يا زميلنا الميسر: {$user->name}");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة. يرجى التأكد من البريد الإلكتروني وكلمة المرور.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}
