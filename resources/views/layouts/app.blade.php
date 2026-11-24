<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'نظام حقيبة الابتكار والتعبير لليافعين') }} - @yield('title', 'الرئيسية')</title>
    
    <!-- خط القاهرة عالي التباين -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* إلغاء الـ radius تماماً وفرض زوايا حادة 90 درجة على كامل عناصر النظام */
        *, *::before, *::after {
            border-radius: 0px !important;
            -webkit-border-radius: 0px !important;
            -moz-border-radius: 0px !important;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #ffffff;
            color: #000000;
            letter-spacing: -0.01em;
        }

        /* تنسيقات الإدخال الحادة بالأبيض والأسود */
        input, select, textarea {
            border: 1px solid #000000 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #000000 !important;
            border-color: #000000 !important;
            box-shadow: none !important;
        }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-black antialiased min-h-screen flex flex-col border-t-4 border-black">

    <!-- شريط التنقل العلوي الحاد (أبيض وأسود) -->
    <header class="bg-black text-white no-print border-b-2 border-black sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                
                <!-- الشعار -->
                <div class="flex items-center gap-4">
                    <a href="{{ Auth::check() ? (Auth::user()->role === 'admin' ? route('admin.dashboard') : route('portal.dashboard')) : route('home') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white text-black font-black text-lg flex items-center justify-center border border-white">
                            KIT
                        </div>
                        <div>
                            <div class="font-black text-sm tracking-wide">حقيبة الابتكار والتعبير لليافعين</div>
                            <div class="text-[10px] text-neutral-400 font-mono">منظومة الإدارة والمتابعة والتقييم M&E</div>
                        </div>
                    </a>
                </div>

                <!-- تقسيم الواجهات بالأبيض والأسود (للمستخدمين المسجلين فقط) -->
                <nav class="hidden md:flex items-center gap-1 text-xs font-bold">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" 
                               class="px-3.5 py-2 border transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black border-white' : 'text-white border-transparent hover:border-white' }}">
                                لوحة التحكم الإدارية
                            </a>
                            <a href="{{ route('admin.dashboard') }}#facilitators-section" 
                               class="px-3.5 py-2 border transition text-neutral-300 border-transparent hover:border-white hover:text-white">
                                ↓ إدارة الميسرين باللوحة
                            </a>
                        @endif

                        <a href="{{ route('portal.dashboard') }}" 
                           class="px-3.5 py-2 border transition {{ request()->routeIs('portal.*') ? 'bg-white text-black border-white' : 'text-white border-transparent hover:border-white' }}">
                            بوابة الإدخال الميداني
                        </a>
                    @endauth
                </nav>

                <!-- بيانات المستخدم وخروج (لا يظهر أي زر دخول للزوار العاديين إطلاقاً) -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden sm:block text-left border-l border-neutral-700 pl-3">
                            <div class="text-xs font-bold leading-none">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-neutral-400 mt-1 font-mono">
                                {{ Auth::user()->role === 'admin' ? '[ مدير النظام ]' : '[ ميسر ميداني ]' }}
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 border border-white text-white hover:bg-white hover:text-black transition text-xs font-bold">
                                تسجيل الخروج
                            </button>
                        </form>
                    @else
                        <!-- روابط الدخول وطلب الحساب للميسرين -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-3 py-1.5 bg-white text-black font-bold text-xs hover:bg-neutral-200 border border-white transition">
                                تسجيل الدخول
                            </a>
                            <a href="{{ route('register') }}" class="px-3 py-1.5 border border-white text-white font-bold text-xs hover:bg-white hover:text-black transition hidden sm:inline-block">
                                طلب حساب ميسر
                            </a>
                        </div>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- شريط الجوال الحاد (للمسجلين فقط) -->
    @auth
    <div class="md:hidden bg-neutral-900 text-white p-2 flex justify-around text-xs font-bold no-print border-b border-black">
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="py-1 px-2 border {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black border-white' : 'border-neutral-700' }}">لوحة التحكم</a>
            <a href="{{ route('admin.dashboard') }}#facilitators-section" class="py-1 px-2 border border-neutral-700">الميسرون (باللوحة)</a>
        @endif
        <a href="{{ route('portal.dashboard') }}" class="py-1 px-2 border {{ request()->routeIs('portal.*') ? 'bg-white text-black border-white' : 'border-neutral-700' }}">بوابة الإدخال</a>
    </div>
    @endauth

    <!-- التنبيهات الحادة -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-6">
        @if (session('success'))
            <div class="mb-6 p-4 border-2 border-black bg-white text-black text-xs font-bold flex items-center justify-between no-print shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <div>[ ✓ ] {{ session('success') }}</div>
                <button onclick="this.parentElement.remove()" class="font-mono text-sm hover:underline">✕ إغلاق</button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="mb-6 p-4 border-2 border-black bg-white text-black text-xs no-print shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-1">
                <div class="font-bold underline">[ ! ] يرجى تصحيح الأخطاء التالية:</div>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- التذييل الحاد -->
    <footer class="bg-black text-white border-t border-black py-4 text-center text-xs no-print mt-auto font-mono">
        حقيبة الابتكار والتعبير لليافعين (Kit Adolescent) &copy; {{ date('Y') }} - تصميم أحادي حاد (Sharp Monochrome)
    </footer>

    @stack('scripts')
</body>
</html>
