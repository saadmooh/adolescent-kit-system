<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'نظام حقيبة الابتكار والتعبير لليافعين') }} - @yield('title', 'الرئيسية')</title>
    
    <!-- خط القاهرة -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS مع ألوان اليونيسف الرسمية -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        unicef: {
                            // ألوان اليونيسف الرسمية — المصدر: github.com/unicef/unicef-material-ui
                            blue: '#1CABE2',
                            darkBlue: '#374EA2',
                            dark: '#002F6C',
                            navy: '#002F6C',
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
            background-color: #F0F9FE;
            color: #002F6C;
            letter-spacing: -0.01em;
        }

        /* أشكال حادة بلا انحناءات — سياسة النظام */
        *, *::before, *::after {
            border-radius: 0 !important;
        }

        /* تحسين الحدود الحادة بألوان اليونيسف */
        .border, [class*="border-"] {
            border-color: #002F6C;
        }

        /* تنسيقات حقول الإدخال بنمط UNICEF Material UI */
        input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([class*="border"]),
        select:not([class*="border"]),
        textarea:not([class*="border"]) {
            border: 1px solid #002F6C;
            background-color: #ffffff;
        }

        input[type="date"] {
            direction: ltr;
            text-align: right;
        }

        input:focus, select:focus, textarea:focus {
            outline: none !important;
            border-color: #1CABE2 !important;
            box-shadow: 0 0 0 3px rgba(28, 171, 226, 0.2) !important;
        }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-unicef-light text-unicef-dark antialiased min-h-screen flex flex-col">

    <!-- شريط التنقل العلوي بنمط اليونيسف الرسمي -->
    <header class="bg-unicef-dark text-white no-print sticky top-0 z-50 shadow-md">
        <!-- خط علوي بأزرق اليونيسف -->
        <div class="h-1 bg-unicef-blue w-full"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                
                <!-- الشعار -->
                <div class="flex items-center gap-4">
                    <a href="{{ Auth::check() ? route('dashboard') : route('home') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-unicef-blue text-white font-black text-lg flex items-center justify-center shadow-sm border-2 border-unicef-yellow">
                            KIT
                        </div>
                        <div>
                            <div class="font-black text-sm tracking-wide text-white">حقيبة الابتكار والتعبير لليافعين</div>
                            <div class="text-[10px] text-cyan-200 font-mono">منظومة المتابعة والتقييم M&E &bull; UNICEF</div>
                        </div>
                    </a>
                </div>

                <!-- بيانات المستخدم وخروج -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden sm:block text-left border-l border-white/20 pl-3">
                            <div class="text-xs font-bold leading-none text-white">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-cyan-200 mt-1 font-mono">
                                {{ Auth::user()->role === 'admin' ? '[ مدير النظام ]' : '[ ' . Auth::user()->roleName() . ' ]' }}
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 border-2 border-white/30 text-white hover:bg-white hover:text-unicef-dark transition text-xs font-bold">
                                تسجيل الخروج
                            </button>
                        </form>
                    @else
                        <!-- روابط الدخول وطلب الحساب -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-4 py-1.5 bg-unicef-blue text-white font-bold text-xs hover:bg-[#0E9BD3] transition shadow-sm border-2 border-unicef-yellow">
                                تسجيل الدخول
                            </a>
                            <a href="{{ route('register') }}" class="px-3.5 py-1.5 border-2 border-white/30 text-white font-bold text-xs hover:bg-white/10 transition">
                                إنشاء حساب جديد
                            </a>
                        </div>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- شريط الجوال (للمسجلين فقط) -->
    @auth
    <div class="md:hidden bg-unicef-dark text-white p-2 flex justify-around text-xs font-bold no-print border-b border-unicef-blue">
        <a href="{{ route('dashboard') }}" class="py-1 px-2 {{ request()->routeIs('dashboard') ? 'bg-unicef-blue text-white' : 'text-unicef-light' }}">اللوحة الموحّدة</a>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.roles.index') }}" class="py-1 px-2 {{ request()->routeIs('admin.roles*') ? 'bg-unicef-blue text-white' : 'text-unicef-light' }}">الأدوار</a>
        @endif
    </div>
    @endauth

    <!-- الحاوية الرئيسية للتنبيهات والمحتوى -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-6">
        @if (session('success'))
            <div class="mb-6 p-4 bg-unicef-green/10 border-2 border-unicef-green text-unicef-green text-xs font-bold flex items-center justify-between no-print shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-unicef-green/20 text-unicef-green flex items-center justify-center font-bold border-2 border-unicef-green">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-unicef-green hover:text-unicef-dark font-bold text-sm">✕</button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="mb-6 p-4 bg-unicef-red/10 border-2 border-unicef-red text-unicef-red text-xs no-print shadow-sm space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <span>⚠️</span>
                    <span>يرجى مراجعة وتصحيح التنبيهات التالية:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pr-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- التذييل بنمط اليونيسف -->
    <footer class="bg-white border-t-2 border-unicef-blue py-6 text-center text-xs no-print mt-auto text-unicef-gray">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 font-bold text-unicef-dark">
                <span class="text-unicef-blue font-black text-sm">UNICEF</span>
                <span>&bull;</span>
                <span>حقيبة الابتكار والتعبير لليافعين (Kit Adolescent) &copy; {{ date('Y') }}</span>
            </div>
            <div class="text-[11px] text-unicef-gray">
                منظومة الإدارة والمتابعة والتقييم الميداني M&E
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>