@extends('layouts.app')

@section('title', 'لوحة التحكم المركزية للإدارة')

@section('content')
<div class="space-y-6"
     x-data="{
        showEditFacilitatorModal: false,
        editFacilitatorUrl: '',
        editFacilitatorName: '',
        editFacilitatorEmail: '',
        editFacilitatorPhone: '',
        editFacilitatorRole: 'facilitator',
        editFacilitatorWilaya: '',
        editFacilitatorMunicipality: '',
        openEditFacilitator(f) {
            this.editFacilitatorUrl = '/admin/facilitators/' + f.id;
            this.editFacilitatorName = f.name;
            this.editFacilitatorEmail = f.email;
            this.editFacilitatorPhone = f.phone || '';
            this.editFacilitatorRole = f.role;
            this.editFacilitatorWilaya = f.wilaya || '';
            this.editFacilitatorMunicipality = f.municipality || '';
            this.showEditFacilitatorModal = true;
        }
     }">

    <!-- الترويسة الحادة للمدير -->
    <div class="bg-black text-white p-6 sm:p-8 border-2 border-black flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <div class="space-y-1">
            <div class="inline-block border border-white px-2 py-0.5 text-[10px] font-mono font-bold uppercase mb-2">
                لوحة القيادة والمتابعة والتقييم M&E
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">إدارة أنشطة حقيبة الابتكار والتعبير لليافعين</h1>
            <p class="text-xs text-neutral-300 max-w-2xl">
                متابعة المؤشرات الوطنية، إدخال وتعديل قائمة الميسرين مباشرة، واعتماد التقارير الميدانية المحالة.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="#facilitators-section" class="px-4 py-2.5 bg-white text-black border border-white hover:bg-neutral-200 font-bold text-xs transition">
                ↓ إدارة الميسرين
            </a>
            <a href="{{ route('portal.sessions.create') }}" class="px-4 py-2.5 bg-neutral-900 text-white border border-white hover:bg-white hover:text-black font-bold text-xs transition">
                + جلسة تدريب
            </a>
            <a href="{{ route('portal.dashboard') }}" class="px-4 py-2.5 border border-white text-white hover:bg-white hover:text-black font-bold text-xs transition">
                بوابة الإدخال الميداني &larr;
            </a>
        </div>
    </div>

    <!-- بطاقات المؤشرات الحادة (Monochrome KPIs) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- الدورات والجلسات -->
        <div class="bg-white p-5 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-black pb-1">
                <span>إجمالي الدورات التدريبية</span>
                <span class="font-mono">[ SESSION ]</span>
            </div>
            <div class="text-3xl font-black text-black font-mono">{{ $totalSessions }}</div>
            <div class="text-[11px] font-bold text-black flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>معتمدة: {{ $approvedSessions }}</span>
                <span>مكتملة: {{ $completedSessions }}</span>
            </div>
        </div>

        <!-- المستفيدين -->
        <div class="bg-white p-5 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-black pb-1">
                <span>اليافعون المستفيدون</span>
                <span class="font-mono">[ ADOLESCENTS ]</span>
            </div>
            <div class="text-3xl font-black text-black font-mono">{{ $totalBeneficiaries }}</div>
            <div class="text-[11px] font-bold text-black flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>ذكور: {{ $maleCount }}</span>
                <span>إناث: {{ $femaleCount }}</span>
            </div>
        </div>

        <!-- الفئات ذات الأولوية -->
        <div class="bg-white p-5 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-black pb-1">
                <span>الفئات ذات الأولوية</span>
                <span class="font-mono">[ INCLUSION ]</span>
            </div>
            <div class="text-3xl font-black text-black font-mono">{{ $disabledCount + $chronicCount }}</div>
            <div class="text-[11px] font-bold text-black flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>ذوو إعاقة: {{ $disabledCount }}</span>
                <span>أمراض مزمنة: {{ $chronicCount }}</span>
            </div>
        </div>

        <!-- الميسرون والمؤسسات -->
        <div class="bg-white p-5 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-black pb-1">
                <span>الميسرون والمؤسسات</span>
                <span class="font-mono">[ NETWORK ]</span>
            </div>
            <div class="text-3xl font-black text-black font-mono">{{ $totalFacilitators }}</div>
            <div class="text-[11px] font-bold text-black flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>طاقم الميسرين: {{ $totalFacilitators }}</span>
                <span>المؤسسات: {{ $totalInstitutions }}</span>
            </div>
        </div>
    </div>

    <!-- قسم إدارة واعتماد قائمة الميسرين مباشرة من لوحة التحكم -->
    <div id="facilitators-section" class="space-y-4">

        @if(session('new_user_credentials'))
            <div class="p-5 border-2 border-black bg-black text-white text-xs space-y-3 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-neutral-700 pb-2 gap-2">
                    <div class="font-black text-sm text-white flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-white text-black font-mono font-bold">[ 🔑 حساب جديد مسجل ]</span>
                        <span>بيانات الدخول المعتمدة للمستخدم المسجل حديثاً</span>
                    </div>
                    <span class="text-[11px] text-neutral-300 font-bold underline">انسخ وسلم هذه البيانات للميسر فوراً:</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 font-mono text-xs">
                    <div class="bg-neutral-900 p-3 border border-neutral-700">
                        <div class="text-[10px] text-neutral-400 uppercase font-sans">الاسم واللقب</div>
                        <div class="font-bold text-white mt-1">{{ session('new_user_credentials')['name'] }}</div>
                    </div>
                    <div class="bg-neutral-900 p-3 border border-neutral-700">
                        <div class="text-[10px] text-neutral-400 uppercase font-sans">البريد الإلكتروني</div>
                        <div class="font-bold text-white mt-1 select-all">{{ session('new_user_credentials')['email'] }}</div>
                    </div>
                    <div class="bg-neutral-900 p-3 border border-neutral-700">
                        <div class="text-[10px] text-neutral-400 uppercase font-sans">الصفة / الدور</div>
                        <div class="font-bold text-white mt-1">{{ session('new_user_credentials')['role'] }}</div>
                    </div>
                    <div class="bg-white p-3 border-2 border-white text-black">
                        <div class="text-[10px] text-neutral-600 uppercase font-sans font-bold">كلمة المرور المؤقتة</div>
                        <div class="font-black text-base mt-0.5 select-all font-mono">{{ session('new_user_credentials')['password'] }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white border-2 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-black pb-3">
                <div>
                    <h2 class="font-black text-base text-black uppercase">
                        [ 👥 ] إدارة واعتماد قائمة الميسرين والمساعدين الميدانيين
                    </h2>
                    <p class="text-[11px] text-neutral-600 mt-0.5">
                        إدخال وتعديل بيانات الميسرين المعتمدين لتظهر تلقائياً في استمارات الجلسات والتقارير الرسمية.
                    </p>
                </div>
                <span class="border border-black px-2.5 py-1 text-xs font-bold bg-black text-white font-mono self-start sm:self-auto">
                    {{ $facilitators->count() }} ميسر معتمد
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- نموذج إدخال ميسر جديد -->
                <div class="lg:col-span-1 bg-white border-2 border-black p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-3"
                     x-data="{ 
                        regPassword: '',
                        generatePassword() {
                            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$';
                            let pass = '';
                            for (let i = 0; i < 10; i++) {
                                pass += chars.charAt(Math.floor(Math.random() * chars.length));
                            }
                            this.regPassword = pass;
                        }
                     }">
                    <div class="border-b-2 border-black pb-2 flex items-center justify-between">
                        <h3 class="font-black text-xs uppercase text-black">[ + ] تسجيل ميسر / مستخدم جديد</h3>
                        <span class="text-[10px] text-neutral-500 font-mono">حصر للإدارة</span>
                    </div>

                    <form action="{{ route('admin.facilitators.store') }}" method="POST" class="space-y-3 text-xs">
                        @csrf

                        <div>
                            <label class="block font-bold text-black mb-1">الاسم واللقب *</label>
                            <input type="text" name="name" required placeholder="مثال: يوسف العربي" class="w-full p-2 text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-black mb-1">البريد الإلكتروني *</label>
                            <input type="email" name="email" required placeholder="youssef@example.com" class="w-full p-2 text-xs font-mono">
                        </div>

                        <div>
                            <label class="block font-bold text-black mb-1">الصفة / الصلاحية *</label>
                            <select name="role" required class="w-full p-2 text-xs font-bold">
                                <option value="facilitator">ميسر رئيسي (Lead Facilitator) - يدير جلسات</option>
                                <option value="assistant">ميسر مساعد (Assistant) - مشارك ميداني</option>
                                <option value="admin">مسؤول نظام (Admin) - تحكم مركزي كامل</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-black mb-1">رقم الهاتف</label>
                            <input type="text" name="phone" placeholder="0661000000" class="w-full p-2 text-xs font-mono">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-bold text-black mb-1">الولاية</label>
                                <input type="text" name="wilaya" placeholder="الجزائر" class="w-full p-2 text-xs">
                            </div>
                            <div>
                                <label class="block font-bold text-black mb-1">البلدية</label>
                                <input type="text" name="municipality" placeholder="بولوغين" class="w-full p-2 text-xs">
                            </div>
                        </div>

                        <!-- حقل كلمة المرور والتوليد العشوائي -->
                        <div class="border-t border-black pt-2 space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="font-bold text-black">كلمة المرور (اختياري)</label>
                                <button type="button" @click="generatePassword()" class="text-[10px] font-mono font-bold text-neutral-800 underline hover:text-black">
                                    [ 🎲 توليد كلمة مرور ]
                                </button>
                            </div>
                            <input type="text" name="password" x-model="regPassword" placeholder="اتركه فارغاً لتوليدها تلقائياً" class="w-full p-2 text-xs font-mono">
                            <p class="text-[10px] text-neutral-500">في حال تركه فارغاً، سيولد النظام كلمة مرور عشوائية آمنة تلقائياً.</p>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-black text-white hover:bg-neutral-800 font-black text-xs uppercase tracking-wider border border-black transition">
                            + حفظ وإدراج المستخدم في القائمة
                        </button>
                    </form>
                </div>

                <!-- جدول استعراض وتعديل وحذف الميسرين -->
                <div class="lg:col-span-2 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                    <div class="p-3 bg-black text-white flex items-center justify-between border-b-2 border-black text-xs font-bold">
                        <span>قائمة الميسرين المعتمدين في النظام</span>
                        <span class="font-mono text-[11px] text-neutral-300">يظهرون فورياً في قوائم الجلسات والتقارير</span>
                    </div>

                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-neutral-100 border-b-2 border-black font-bold sticky top-0">
                                <tr>
                                    <th class="py-2.5 px-3">الاسم واللقب</th>
                                    <th class="py-2.5 px-3">الصفة</th>
                                    <th class="py-2.5 px-3">الهاتف</th>
                                    <th class="py-2.5 px-3">الموقع</th>
                                    <th class="py-2.5 px-3 text-center">الجلسات</th>
                                    <th class="py-2.5 px-3 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black font-medium">
                                @forelse($facilitators as $f)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="py-2.5 px-3 font-bold text-black">
                                            <div>{{ $f->name }}</div>
                                            <div class="text-[10px] text-neutral-500 font-mono">{{ $f->email }}</div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            @if($f->role === 'admin')
                                                <span class="border border-black px-1.5 py-0.5 bg-black text-white text-[10px] font-bold">مدير</span>
                                            @elseif($f->role === 'facilitator')
                                                <span class="border border-black px-1.5 py-0.5 bg-white text-black text-[10px] font-bold">ميسر رئيسي</span>
                                            @else
                                                <span class="border border-black px-1.5 py-0.5 bg-neutral-200 text-black text-[10px] font-bold">مساعد</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 font-mono">{{ $f->phone ?? '—' }}</td>
                                        <td class="py-2.5 px-3">
                                            {{ $f->municipality ? $f->municipality . ' (' . $f->wilaya . ')' : '—' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-mono font-bold">
                                            {{ $f->lead_sessions_count }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" 
                                                        @click="openEditFacilitator({{ $f->toJson() }})" 
                                                        class="border border-black px-2 py-0.5 text-[10px] font-bold bg-white text-black hover:bg-black hover:text-white transition">
                                                    ✏️ تعديل
                                                </button>
                                                @if($f->id !== Auth::id())
                                                    <form action="{{ route('admin.facilitators.destroy', $f) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد حذف هذا الميسر؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="border border-black px-1.5 py-0.5 text-[10px] font-bold hover:bg-black hover:text-white transition" title="حذف الميسر">
                                                            🗑️
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-neutral-400 text-[10px]">(أنت)</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-neutral-400">لا يوجد ميسرون مسجلون.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- صندوق الجلسات التي بانتظار المصادقة -->
    @if($pendingApprovals->count() > 0)
        <div class="bg-white border-2 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-4">
            <div class="flex items-center justify-between border-b-2 border-black pb-2">
                <h2 class="font-black text-black text-sm uppercase">
                    [ ! ] جلسات وتقارير نشاط بانتظار الاعتماد والمصادقة الإدارية
                </h2>
                <span class="border border-black px-2 py-0.5 text-xs font-bold bg-black text-white font-mono">
                    {{ $pendingApprovals->count() }} بانتظار القرار
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($pendingApprovals as $pending)
                    <div class="bg-white p-4 border border-black flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-sm text-black">{{ $pending->title }}</span>
                                <span class="text-xs font-mono">{{ $pending->session_date->format('Y/m/d') }}</span>
                            </div>
                            <div class="text-xs text-neutral-600 space-y-0.5">
                                <div>المؤسسة: <strong>{{ $pending->institution->name }}</strong> (بلدية {{ $pending->municipality }})</div>
                                <div>الميسر المقرر: <strong>{{ $pending->leadFacilitator1->name ?? 'غير محدد' }}</strong></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-black">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.sessions.edit', $pending) }}" class="text-xs font-bold text-black underline">✏️ تعديل الجلسة</a>
                                <span>|</span>
                                <a href="{{ route('portal.sessions.report', $pending) }}" class="text-xs font-bold text-black underline">مراجعة التقرير &larr;</a>
                                <span>|</span>
                                <a href="{{ route('sessions.print.report', $pending) }}" target="_blank" class="text-xs font-bold text-neutral-600 underline">معاينة للطباعة</a>
                            </div>

                            <form action="{{ route('admin.sessions.approve', $pending) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-black text-white hover:bg-neutral-800 text-xs font-bold border border-black transition">
                                    [ ✓ ] اعتماد ومصادقة
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- جدول إدارة الجلسات الحاد -->
    <div class="bg-white border-2 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
        <div class="p-4 bg-black text-white flex items-center justify-between border-b-2 border-black text-xs font-bold">
            <span>سجل الأنشطة والتدريبات الميدانية (كافة الجلسات)</span>
            <span class="font-mono">إجمالي: {{ $recentSessions->count() }} جلسة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-neutral-100 border-b-2 border-black font-bold">
                    <tr>
                        <th class="py-3 px-3">عنوان التدريب</th>
                        <th class="py-3 px-3">التاريخ</th>
                        <th class="py-3 px-3">المؤسسة والبلدية</th>
                        <th class="py-3 px-3">الميسر المسؤول</th>
                        <th class="py-3 px-3 text-center">المقاعد</th>
                        <th class="py-3 px-3">حالة الاعتماد</th>
                        <th class="py-3 px-3 text-center">الإجراءات والتحكم والطباعة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black font-medium">
                    @forelse($recentSessions as $session)
                        <tr class="hover:bg-neutral-50 transition">
                            <td class="py-3 px-3 font-bold text-black">{{ $session->title }}</td>
                            <td class="py-3 px-3 whitespace-nowrap font-mono">{{ $session->session_date->format('Y/m/d') }}</td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-black">{{ $session->institution->name }}</div>
                                <div class="text-[10px] text-neutral-500">بلدية {{ $session->municipality }} ({{ $session->wilaya }})</div>
                            </td>
                            <td class="py-3 px-3">{{ $session->leadFacilitator1->name ?? '—' }}</td>
                            <td class="py-3 px-3 text-center font-mono font-bold">
                                {{ $session->attendances->count() }}
                            </td>
                            <td class="py-3 px-3">
                                @if($session->status === 'approved')
                                    <span class="border border-black px-2 py-0.5 bg-black text-white text-[10px] font-bold">معتمد رسمياً</span>
                                @elseif($session->status === 'completed')
                                    <span class="border border-black px-2 py-0.5 bg-white text-black text-[10px] font-bold">بانتظار الاعتماد</span>
                                @else
                                    <span class="border border-neutral-400 px-2 py-0.5 bg-neutral-100 text-black text-[10px] font-bold">مسودة</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                    <a href="{{ route('admin.sessions.edit', $session) }}" class="border border-black px-2 py-1 bg-black text-white hover:bg-neutral-800 font-bold transition text-[11px]">
                                        ✏️ تعديل
                                    </a>
                                    <a href="{{ route('portal.sessions.attendance', $session) }}" class="border border-black px-2 py-1 bg-neutral-100 hover:bg-black hover:text-white font-bold transition text-[11px]">
                                        👥 الحضور
                                    </a>
                                    <a href="{{ route('portal.sessions.report', $session) }}" class="border border-black px-2 py-1 bg-neutral-100 hover:bg-black hover:text-white font-bold transition text-[11px]">
                                        📑 التقرير
                                    </a>
                                    <a href="{{ route('sessions.print.attendance', $session) }}" target="_blank" class="border border-black px-1.5 py-1 bg-white hover:bg-neutral-200 font-bold transition text-[11px]" title="طباعة كشف الحضور">
                                        🖨️ الكشف
                                    </a>
                                    <a href="{{ route('sessions.print.report', $session) }}" target="_blank" class="border border-black px-1.5 py-1 bg-white hover:bg-neutral-200 font-bold transition text-[11px]" title="طباعة تقرير النشاط">
                                        🖨️ التقرير
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-neutral-400">لا توجد جلسات مسجلة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- نافذة منبثقة حادة لتعديل بيانات الميسر -->
    <div x-show="showEditFacilitatorModal" class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] max-w-lg w-full p-6 space-y-4" @click.away="showEditFacilitatorModal = false">
            <div class="flex items-center justify-between border-b-2 border-black pb-2">
                <h3 class="font-black text-sm uppercase text-black">✏️ تعديل بيانات الميسر المعتمد</h3>
                <button @click="showEditFacilitatorModal = false" class="text-black font-black text-base border border-black px-2 py-0.5 hover:bg-black hover:text-white">✕</button>
            </div>

            <form :action="editFacilitatorUrl" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-black mb-1">الاسم واللقب *</label>
                    <input type="text" name="name" x-model="editFacilitatorName" required class="w-full p-2 text-xs font-bold">
                </div>

                <div>
                    <label class="block font-bold text-black mb-1">البريد الإلكتروني *</label>
                    <input type="email" name="email" x-model="editFacilitatorEmail" required class="w-full p-2 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-bold text-black mb-1">الصفة / الدور *</label>
                    <select name="role" x-model="editFacilitatorRole" required class="w-full p-2 text-xs font-bold">
                        <option value="facilitator">ميسر رئيسي (Lead Facilitator)</option>
                        <option value="assistant">ميسر مساعد (Assistant Facilitator)</option>
                        <option value="admin">مسؤول إدارة ونظام (Admin)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-black mb-1">رقم الهاتف</label>
                    <input type="text" name="phone" x-model="editFacilitatorPhone" class="w-full p-2 text-xs font-mono">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-black mb-1">الولاية</label>
                        <input type="text" name="wilaya" x-model="editFacilitatorWilaya" class="w-full p-2 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-black mb-1">البلدية</label>
                        <input type="text" name="municipality" x-model="editFacilitatorMunicipality" class="w-full p-2 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-black mb-1">كلمة مرور جديدة (اتركها فارغة لعدم التغيير)</label>
                    <input type="password" name="password" placeholder="أدخل كلمة مرور جديدة أو اترك فارغاً" class="w-full p-2 text-xs font-mono">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t-2 border-black">
                    <button type="button" @click="showEditFacilitatorModal = false" class="px-4 py-2 border border-black text-black hover:bg-neutral-100 font-bold">إلغاء</button>
                    <button type="submit" class="px-6 py-2 bg-black text-white hover:bg-neutral-800 font-black uppercase border border-black">
                        [ ✓ ] حفظ وتحديث البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
