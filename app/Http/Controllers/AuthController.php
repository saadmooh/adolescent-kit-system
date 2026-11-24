<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ReportAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('portal.dashboard');
        }

        $wilayasData = ReportAggregationService::WILAYAS_DATA;

        return view('auth.register', compact('wilayasData'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:50',
            'role' => 'required|in:facilitator,assistant',
            'wilaya' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'يرجى إدخال الاسم واللقب كاملاً.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً في المنظومة.',
            'phone.required' => 'يرجى إدخال رقم الهاتف للتواصل.',
            'role.required' => 'يرجى تحديد الصفة (ميسر رئيسي أو مساعد).',
            'wilaya.required' => 'يرجى اختيار الولاية.',
            'municipality.required' => 'يرجى اختيار البلدية.',
            'password.required' => 'يرجى تعيين كلمة المرور.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق مع كلمة المرور المدخلة.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'wilaya' => $validated['wilaya'],
            'municipality' => $validated['municipality'],
            'password' => Hash::make($validated['password']),
            'approval_status' => 'pending',
            'is_active' => false,
            'approved_at' => null,
        ]);

        return redirect()->route('login')
            ->with('success', 'تم إرسال طلب تسجيل حسابك بنجاح! حسابك حالياً قيد المراجعة، وستتمكن من تسجيل الدخول وإدخال بيانات الأنشطة فور اعتماد وتفعيل الحساب من طرف مدير النظام.');
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
            $user = Auth::user();

            // فحص شرط اعتماد وتفعيل الحساب من طرف مدير النظام
            if (!$user->isApproved() && $user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($user->isPending()) {
                    return back()->withErrors([
                        'email' => 'حسابك مسجل وبانتظار اعتماد وموافقة مدير النظام. لا يمكنك الدخول أو إدخال بيانات الأنشطة حتى يتم قبول وتفعيل حسابك من طرف الإدارة.',
                    ])->onlyInput('email');
                }

                if ($user->isRejected()) {
                    return back()->withErrors([
                        'email' => 'عذراً، تم رفض طلب تفعيل هذا الحساب من طرف الإدارة. يرجى مراجعة مسؤول المنظومة.',
                    ])->onlyInput('email');
                }

                return back()->withErrors([
                    'email' => 'حسابك معطل حالياً من طرف إدارة المنظومة. يرجى مراجعة مدير النظام.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

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
