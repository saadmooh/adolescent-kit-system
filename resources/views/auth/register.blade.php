<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب تسجيل حساب ميسر جديد - حقيبة الابتكار والتعبير لليافعين</title>
    
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
        input, select {
            border: 1px solid #000000 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        input:focus, select:focus {
            outline: 2px solid #000000 !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-neutral-100">

    <div class="max-w-xl w-full space-y-4 my-8" 
         x-data="{
            wilayasData: {{ json_encode($wilayasData, JSON_UNESCAPED_UNICODE) }},
            selectedWilaya: 'الجزائر',
            selectedMunicipality: 'باب الوادي',
            onWilayaChange() {
                const munics = this.wilayasData[this.selectedWilaya] || [];
                this.selectedMunicipality = munics.length > 0 ? munics[0] : '';
            }
         }">
        
        <!-- البطاقة الحادة بالأبيض والأسود -->
        <div class="bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] p-6 sm:p-8 space-y-6">
            
            <!-- الترويسة والشعار -->
            <div class="text-center space-y-2 border-b-2 border-black pb-5">
                <div class="w-14 h-14 mx-auto bg-black text-white flex items-center justify-center font-black text-xl border-2 border-black">
                    KIT
                </div>
                <h1 class="text-lg font-black text-black uppercase tracking-tight">طلب تسجيل حساب ميسر ميداني جديد</h1>
                <div class="inline-block border border-black px-2.5 py-0.5 text-[10px] font-mono font-bold bg-neutral-100">
                    منظومة حقيبة الابتكار والتعبير لليافعين (Kit Adolescent)
                </div>
            </div>

            <!-- تنبيه شرط موافقة مدير النظام -->
            <div class="p-3 border-2 border-black bg-black text-white text-xs space-y-1">
                <div class="font-bold text-yellow-300">[ ℹ️ تنبيه أمني وإداري ]</div>
                <p class="text-[11px] text-neutral-200">
                    إنشاء الحسابات مشروط بـ <strong>قبول ومصادقة مدير النظام</strong>. لن تتمكن من تسجيل الدخول وإدخال بيانات الأنشطة الميدانية إلا بعد قيام الإدارة بتفعيل حسابك رسمياً.
                </p>
            </div>

            <!-- تنبيه الأخطاء -->
            @if ($errors->any())
                <div class="p-3 border-2 border-black bg-neutral-100 text-black text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>[ ! ] {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- استمارة طلب التسجيل -->
            <form action="{{ route('register.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-black mb-1">الاسم واللقب كاملاً *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full p-2.5 text-xs font-bold"
                            placeholder="مثال: كريم بلحاج">
                    </div>

                    <div>
                        <label class="block font-bold text-black mb-1">البريد الإلكتروني المهني / الشخصي *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full p-2.5 text-xs font-mono"
                            placeholder="karim@domain.dz">
                    </div>

                    <div>
                        <label class="block font-bold text-black mb-1">رقم الهاتف للتواصل *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                            class="w-full p-2.5 text-xs font-mono"
                            placeholder="0661000000">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-black mb-1">الصفة الميدانية المطلوبة *</label>
                        <select name="role" required class="w-full p-2.5 text-xs font-bold">
                            <option value="facilitator" {{ old('role') === 'facilitator' ? 'selected' : '' }}>
                                ميسر رئيسي (Lead Facilitator) - مسؤول عن تسيير الجلسات وإدخال التقارير
                            </option>
                            <option value="assistant" {{ old('role') === 'assistant' ? 'selected' : '' }}>
                                ميسر مساعد (Assistant Facilitator) - مشارك في التنشيط وكشوف الحضور
                            </option>
                        </select>
                    </div>

                    <!-- الولاية والبلدية -->
                    <div>
                        <label class="block font-bold text-black mb-1">الولاية *</label>
                        <select name="wilaya" x-model="selectedWilaya" @change="onWilayaChange()" required class="w-full p-2.5 text-xs font-bold">
                            <template x-for="(munics, wName) in wilayasData" :key="wName">
                                <option :value="wName" x-text="wName" :selected="wName === selectedWilaya"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-black mb-1">البلدية *</label>
                        <select name="municipality" x-model="selectedMunicipality" required class="w-full p-2.5 text-xs font-bold">
                            <template x-for="m in (wilayasData[selectedWilaya] || [])" :key="m">
                                <option :value="m" x-text="m" :selected="m === selectedMunicipality"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-black mb-1">كلمة المرور (8 أحرف على الأقل) *</label>
                        <input type="password" name="password" required
                            class="w-full p-2.5 text-xs font-mono"
                            placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block font-bold text-black mb-1">تأكيد كلمة المرور *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full p-2.5 text-xs font-mono"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-black text-white border-2 border-black hover:bg-neutral-800 font-black text-xs uppercase tracking-wider transition">
                        [ ✉️ ] إرسال طلب تسجيل الحساب للمدير
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t-2 border-black flex items-center justify-between text-xs">
                <span class="text-neutral-600">لديك حساب معتمد بالفعل؟</span>
                <a href="{{ route('login') }}" class="font-bold text-black underline hover:bg-black hover:text-white px-1">
                    تسجيل الدخول &larr;
                </a>
            </div>

        </div>

        <div class="text-center text-[11px] text-neutral-500 font-mono">
            حقيبة الابتكار والتعبير لليافعين (Kit Adolescent) &copy; {{ date('Y') }}
        </div>

    </div>

</body>
</html>
