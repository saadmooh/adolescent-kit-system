<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام حقيبة الابتكار والتعبير لليافعين</title>
    
    <!-- خط القاهرة -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after {
            border-radius: 0px !important;
        }
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #ffffff;
            color: #000000;
        }
        input {
            border: 1px solid #000000 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        input:focus {
            outline: 2px solid #000000 !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-neutral-100">

    <div class="max-w-md w-full space-y-4" x-data="{ email: '', password: '' }">
        
        <!-- البطاقة الحادة بالأبيض والأسود -->
        <div class="bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] p-8 space-y-6">
            
            <!-- الترويسة والشعار -->
            <div class="text-center space-y-2 border-b-2 border-black pb-5">
                <div class="w-14 h-14 mx-auto bg-black text-white flex items-center justify-center font-black text-xl border-2 border-black">
                    KIT
                </div>
                <h1 class="text-lg font-black text-black uppercase tracking-tight">حقيبة الابتكار والتعبير لليافعين</h1>
                <p class="text-xs text-neutral-600">منظومة الإدارة والمتابعة والتقييم M&E</p>
            </div>

            <!-- تنبيه الأخطاء -->
            @if ($errors->any())
                <div class="p-3 border-2 border-black bg-neutral-100 text-black text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>[ ! ] {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="p-3 border-2 border-black bg-black text-white text-xs font-bold">
                    [ ✓ ] {{ session('success') }}
                </div>
            @endif

            <!-- نموذج تسجيل الدخول -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div>
                    <label class="block font-bold text-black mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" x-model="email" required autofocus
                        class="w-full p-2.5 text-xs font-mono"
                        placeholder="admin@adolescent-kit.org">
                </div>

                <div>
                    <label class="block font-bold text-black mb-1">كلمة المرور</label>
                    <input type="password" name="password" x-model="password" required
                        class="w-full p-2.5 text-xs font-mono"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="border border-black">
                        <span class="text-black font-semibold text-[11px]">تذكر تسجيل الدخول</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-black text-white border-2 border-black hover:bg-white hover:text-black font-black text-xs uppercase tracking-wider transition">
                    تسجيل الدخول
                </button>
            </form>

            <!-- خيارات الدخول السريع -->
            <div class="pt-4 border-t-2 border-black space-y-2">
                <div class="text-[11px] font-bold text-neutral-500 text-center">أو اختر حساباً جاهزاً للتجربة:</div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="email = 'admin@adolescent-kit.org'; password = 'password123'"
                        class="p-2 border border-black bg-white hover:bg-black hover:text-white text-black text-[11px] font-bold transition">
                        👑 مدير النظام
                    </button>
                    <button type="button" @click="email = 'facilitator1@adolescent-kit.org'; password = 'password123'"
                        class="p-2 border border-black bg-white hover:bg-black hover:text-white text-black text-[11px] font-bold transition">
                        📋 ميسر ميداني
                    </button>
                </div>
            </div>

        </div>

        <div class="text-center text-[11px] text-neutral-500 font-mono">
            نظام حقيبة الابتكار والتعبير لليافعين &copy; {{ date('Y') }}
        </div>

    </div>

</body>
</html>
