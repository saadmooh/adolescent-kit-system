<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - حقيبة الابتكار والتعبير لليافعين</title>
    
    <!-- خط القاهرة -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        unicef: {
                            blue: '#1CABE2',
                            dark: '#002F6C',
                            cyan: '#00AEEF',
                            light: '#F0F9FE',
                            yellow: '#FFC20E',
                            green: '#00B589',
                            red: '#E2231A',
                            gray: '#5A6A80'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #F0F8FD 0%, #FFFFFF 100%);
            color: #1C355E;
        }

        /* أشكال حادة بلا انحناءات */
        *, *::before, *::after {
            border-radius: 0 !important;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-[420px] w-full" x-data="{ showPassword: false }">
        
        <!-- بطاقة تسجيل الدخول بنمط اليونيسف الرسمي -->
        <div class="bg-white shadow-[0_10px_35px_-5px_rgba(28,171,226,0.15)] border-2 border-unicef-dark p-8 space-y-6">
            
            <!-- الترويسة والشعار -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-unicef-light text-unicef-blue mb-1 shadow-sm border-2 border-unicef-dark">
                    <span class="font-black text-xl">KIT</span>
                </div>
                <h1 class="text-2xl font-black text-unicef-dark">تسجيل الدخول</h1>
                <p class="text-xs font-semibold text-slate-400">بوابة الميسرين وإدارة منظومة اليافعين</p>
            </div>

            <!-- تنبيه الأخطاء إن وجدت -->
            @if ($errors->any())
                <div class="p-3.5 bg-red-50 border-2 border-red-600 text-red-700 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1">
                        <span>⚠️</span>
                        <span>تنبيه الدخول:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] pr-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="p-3.5 bg-emerald-50 border-2 border-emerald-600 text-emerald-800 text-xs font-bold flex items-center gap-2">
                    <span class="text-emerald-600 text-base">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- نموذج تسجيل الدخول -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition"
                        placeholder="user@domain.dz">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">كلمة المرور</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" required
                            class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition pr-10"
                            placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 hover:text-unicef-blue transition">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="accent-unicef-blue w-3.5 h-3.5">
                        <span class="text-slate-600 text-[11px] font-semibold">تذكر تسجيل دخولي</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 bg-unicef-blue hover:bg-[#0E9BD3] text-white font-bold text-sm shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] transition flex items-center justify-center gap-2 border-2 border-unicef-dark">
                    <span>تسجيل الدخول</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- رابط إنشاء حساب جديد -->
            <div class="pt-2 text-center text-xs text-slate-500 border-t border-unicef-dark">
                <span>ليس لديك حساب ميسر بعد؟</span>
                <a href="{{ route('register') }}" class="font-bold text-unicef-blue hover:underline mr-1">
                    طلب إنشاء حساب جديد
                </a>
            </div>

        </div>

        <div class="text-center text-[11px] text-slate-400 mt-4 flex items-center justify-center gap-2">
            <span>منظومة حقيبة الابتكار والتعبير لليافعين (Kit Adolescent)</span>
            <span>&bull;</span>
            <span class="font-bold text-unicef-blue">UNICEF</span>
        </div>

    </div>

</body>
</html>