<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - حقيبة الابتكار والتعبير لليافعين</title>
    
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
<body class="min-h-screen flex items-center justify-center p-4 py-8">

    <div class="max-w-[460px] w-full"
         x-data="{
            showPassword: false,
            wilayasData: {{ json_encode($wilayasData, JSON_UNESCAPED_UNICODE) }},
            selectedWilaya: '{{ old('wilaya', '') }}'
         }">
        
        <!-- بطاقة التسجيل بنمط اليونيسف ومطابقة للصورة المرفقة -->
        <div class="bg-white shadow-[0_10px_35px_-5px_rgba(28,171,226,0.15)] border-2 border-unicef-dark p-8 space-y-6">
            
            <!-- الترويسة والشعار -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-unicef-light text-unicef-blue mb-1 border-2 border-unicef-dark">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-unicef-dark">إنشاء حساب جديد</h1>
                <p class="text-xs font-semibold text-slate-400">انضم إلينا وابدأ بالتسجيل الآن</p>
            </div>

            <!-- تنبيه الأخطاء إن وجدت -->
            @if ($errors->any())
                <div class="p-3.5 bg-red-50 border-2 border-red-600 text-red-700 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1">
                        <span>⚠️</span>
                        <span>يرجى تصحيح الأخطاء التالية:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] pr-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- استمارة التسجيل -->
            <form action="{{ route('register.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <!-- 1. الاسم الكامل -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition"
                        placeholder="الاسم واللقب">
                </div>

                <!-- 2. البريد الإلكتروني -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition"
                        placeholder="example@domain.com">
                </div>

                <!-- 3. كلمة المرور مع زر العين -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">كلمة المرور</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" required
                            class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition pr-10"
                            placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 hover:text-unicef-blue transition">
                            <!-- أيقونة العين -->
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1">8 أحرف أو أكثر</div>
                </div>

                <!-- 4. رقم الهاتف -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition text-right"
                        placeholder="05XXXXXXXX">
                </div>

                <!-- 5. الولاية (اختيار) والبلدية (كتابة يدوية) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 text-xs">الولاية</label>
                        <select name="wilaya" x-model="selectedWilaya" required
                            class="w-full px-3 py-2.5 border-2 border-unicef-dark text-xs text-slate-800 focus:outline-none focus:bg-unicef-light transition bg-white">
                            <option value="">— اختر الولاية —</option>
                            <template x-for="(munics, wName) in wilayasData" :key="wName">
                                <option :value="wName" x-text="wName" :selected="wName === selectedWilaya"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 text-xs">اسم البلدية</label>
                        <input type="text" name="municipality" value="{{ old('municipality') }}" required
                            class="w-full px-3 py-2.5 border-2 border-unicef-dark text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition"
                            placeholder="اكتب اسم البلدية">
                    </div>
                </div>

                <!-- 6. العنوان الكامل -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">العنوان الكامل</label>
                    <textarea name="address" rows="2" required
                        class="w-full px-3.5 py-2 border-2 border-unicef-dark text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-unicef-light transition resize-none"
                        placeholder="العنوان الكامل: الحي، الشارع، رقم المسكن...">{{ old('address') }}</textarea>
                </div>

                <!-- إشعار شرط تفعيل الحساب من طرف المدير -->
                <div class="p-3 bg-unicef-light border-2 border-unicef-blue text-unicef-dark text-[11px] flex items-start gap-2">
                    <span class="text-unicef-blue font-bold text-sm">ℹ️</span>
                    <div>
                        <strong>تنبيه:</strong> سيتم إرسال حسابك للمراجعة؛ وستتمكن من تسجيل الدخول فور اعتماد الحساب من طرف مدير المنظومة.
                    </div>
                </div>

                <!-- زر الإرسال بنمط اليونيسف الرسمي -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3 px-4 bg-unicef-blue hover:bg-[#0E9BD3] text-white font-bold text-sm shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] transition flex items-center justify-center gap-2 border-2 border-unicef-dark">
                        <span>إنشاء حساب جديد</span>
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- رابط تسجيل الدخول -->
            <div class="pt-2 text-center text-xs text-slate-500 border-t border-unicef-dark">
                <span>لديك حساب بالفعل؟</span>
                <a href="{{ route('login') }}" class="font-bold text-unicef-blue hover:underline mr-1">
                    تسجيل الدخول
                </a>
            </div>

        </div>

        <!-- الشعار وحقوق المنظومة -->
        <div class="text-center text-[11px] text-slate-400 mt-4 flex items-center justify-center gap-2">
            <span>منظومة حقيبة الابتكار والتعبير لليافعين (Kit Adolescent)</span>
            <span>&bull;</span>
            <span class="font-bold text-unicef-blue">UNICEF</span>
        </div>

    </div>

</body>
</html>