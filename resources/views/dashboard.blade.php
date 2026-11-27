@extends('layouts.app')

@section('title', 'لوحة التحكم الموحّدة')

@section('content')
<div class="space-y-6" 
     x-data="{
        showCreateModal: false,
        showPreviewModal: false,
        previewSession: null,
        showFacEditModal: false,
        facEditAction: '',
        facName: '',
        facEmail: '',
        facRole: 'facilitator',
        facPhone: '',
        facWilaya: '',
        facMunicipality: '',
        facPassword: '',
        step: 1,
        institutions: {{ $institutions->toJson() }},
        wilayasData: {{ json_encode($wilayasData, JSON_UNESCAPED_UNICODE) }},
        institutionName: '',
        selectedWilaya: 'الجزائر',
        selectedMunicipality: 'باب الوادي',
        lead1: '{{ Auth::id() }}',
        lead2: '',
        assistants: ['', '', '', '', '', ''],
        sessionTitle: 'تدريب حقيبة الابتكار والتعبير لليافعين Kit Adolescent',
        sessionDate: '{{ date('Y-m-d') }}',
        venue: '',
        transitFrom: '',
        transitTo: '',

        onWilayaChange() {
            const munics = this.wilayasData[this.selectedWilaya] || [];
            this.selectedMunicipality = munics.length > 0 ? munics[0] : '';
        },

        openPreview(s) {
            this.previewSession = s;
            this.showPreviewModal = true;
        },

        openFacEdit(u) {
            this.facEditAction = u.action;
            this.facName = u.name || '';
            this.facEmail = u.email || '';
            this.facRole = u.role || 'facilitator';
            this.facPhone = u.phone || '';
            this.facWilaya = u.wilaya || '';
            this.facMunicipality = u.municipality || '';
            this.facPassword = '';
            this.showFacEditModal = true;
        },

        resetModal() {
            this.step = 1;
            this.institutionName = '';
            this.assistants = ['', '', '', '', '', ''];
            this.venue = '';
            this.transitFrom = '';
            this.transitTo = '';
        }
     }">

    <!-- الترويسة الموحّدة -->
    <div class="bg-unicef-dark text-white p-6 sm:p-8 border-2 border-unicef-dark flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)]">
        <div class="space-y-1">
            <div class="inline-block border border-white px-2 py-0.5 text-[10px] font-mono font-bold uppercase mb-2">
                لوحة القيادة والمتابعة والتقييم M&E
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">أهلاً بك، {{ $user->name }}</h1>
            <p class="text-xs text-neutral-300 max-w-2xl">
                لوحة تحكم موحّدة — تظهر الأقسام حسب صلاحيات دورك: <span class="font-bold">{{ $user->roleName() }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @php if ($can('sessions.create')): @endphp
                <button type="button" @click="resetModal(); showCreateModal = true"
                    class="px-4 py-2.5 bg-white text-unicef-dark border border-white hover:bg-neutral-200 font-bold text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ نشاط جديد</span>
                </button>
            @php endif; @endphp

            @php if ($can('users.approve') && isset($pendingUsers) && $pendingUsers->count() > 0): @endphp
                <a href="#approvals-section" class="px-4 py-2.5 bg-white text-unicef-dark border border-white hover:bg-neutral-200 font-bold text-xs transition">
                    ↓ طلبات الاعتماد ({{ $pendingUsers->count() }})
                </a>
            @php endif; @endphp

            @php if ($can('users.view')): @endphp
                <a href="#facilitators-section" class="px-4 py-2.5 border border-white text-white hover:bg-white hover:text-unicef-dark font-bold text-xs transition">
                    إدارة الميسرين
                </a>
            @php endif; @endphp

            @php if ($can('roles.manage')): @endphp
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2.5 bg-unicef-darkBlue text-white border border-white hover:bg-white hover:text-unicef-dark font-bold text-xs transition">
                    الأدوار والصلاحيات
                </a>
            @php endif; @endphp
        </div>
    </div>

    <!-- بطاقات المؤشرات (للأدوار التي تملك stats.view) -->
    @php if ($can('stats.view')): @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 border-2 border-unicef-dark shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-unicef-dark pb-1">
                <span>إجمالي الدورات التدريبية</span>
                <span class="font-mono">[ SESSION ]</span>
            </div>
            <div class="text-3xl font-black text-unicef-dark font-mono">{{ $totalSessions }}</div>
            <div class="text-[11px] font-bold text-unicef-dark flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>معتمدة: {{ $approvedSessions }}</span>
                <span>مكتملة: {{ $completedSessions }}</span>
            </div>
        </div>

        <div class="bg-white p-5 border-2 border-unicef-dark shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-unicef-dark pb-1">
                <span>اليافعون المستفيدون</span>
                <span class="font-mono">[ ADOLESCENTS ]</span>
            </div>
            <div class="text-3xl font-black text-unicef-dark font-mono">{{ $totalBeneficiaries }}</div>
            <div class="text-[11px] font-bold text-unicef-dark flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>ذكور: {{ $maleCount }}</span>
                <span>إناث: {{ $femaleCount }}</span>
            </div>
        </div>

        <div class="bg-white p-5 border-2 border-unicef-dark shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-unicef-dark pb-1">
                <span>الفئات ذات الأولوية</span>
                <span class="font-mono">[ INCLUSION ]</span>
            </div>
            <div class="text-3xl font-black text-unicef-dark font-mono">{{ ($disabledCount ?? 0) + ($chronicCount ?? 0) }}</div>
            <div class="text-[11px] font-bold text-unicef-dark flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>ذوو إعاقة: {{ $disabledCount ?? 0 }}</span>
                <span>أمراض مزمنة: {{ $chronicCount ?? 0 }}</span>
            </div>
        </div>

        <div class="bg-white p-5 border-2 border-unicef-dark shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-unicef-dark pb-1">
                <span>الميسرون والمؤسسات</span>
                <span class="font-mono">[ NETWORK ]</span>
            </div>
            <div class="text-3xl font-black text-unicef-dark font-mono">{{ $totalFacilitators ?? 0 }}</div>
            <div class="text-[11px] font-bold text-unicef-dark flex items-center justify-between pt-1 border-t border-neutral-200">
                <span>طاقم الميسرين: {{ $totalFacilitators ?? 0 }}</span>
                <span>المؤسسات: {{ $totalInstitutions }}</span>
            </div>
        </div>
    </div>
    @php endif; @endphp

    <!-- طلبات التسجيل بانتظار الاعتماد (لدور users.approve) -->
    @php if ($can('users.approve') && isset($pendingUsers) && $pendingUsers->count() > 0): @endphp
    <section id="approvals-section" class="bg-white border-4 border-unicef-dark p-6 shadow-[8px_8px_0px_0px_rgba(0,47,108,1)] space-y-4">
        <div class="flex items-center justify-between border-b-2 border-unicef-dark pb-3">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-red-600 inline-block animate-pulse"></span>
                <h2 class="font-black text-sm text-unicef-dark uppercase">
                    [ ⏳ ] طلبات تسجيل ميسرين ومساعدين جدد بانتظار اعتماد وموافقة الإدارة
                </h2>
            </div>
            <span class="border border-unicef-dark px-2.5 py-0.5 text-xs font-bold bg-unicef-dark text-white font-mono">
                {{ $pendingUsers->count() }} طلب قيد المراجعة
            </span>
        </div>

        <p class="text-xs text-neutral-700">
            الحسابات التالية قام أصحابها بتقديم طلب التسجيل عبر البوابة؛ <strong>ولن يتمكنوا من تسجيل الدخول وإدخال أو استعراض أي بيانات حتى تقوم بقبول طلباتهم وتفعيل حساباتهم رسمياً مع اختيار الدور المناسب.</strong>
        </p>

        <div class="space-y-3 divide-y divide-unicef-dark">
            @foreach($pendingUsers as $pu)
            <div class="p-4 grid sm:grid-cols-2 gap-4 items-end">
                <div class="space-y-1 text-xs">
                    <p class="font-bold text-unicef-dark text-sm">{{ $pu->name }}</p>
                    <p class="font-mono">{{ $pu->email }}</p>
                    <p class="font-mono">{{ $pu->phone ?? '—' }}</p>
                    <p>{{ $pu->municipality ? $pu->municipality . ' (' . $pu->wilaya . ')' : '—' }}</p>
                </div>

                <form action="{{ route('admin.facilitators.approve', $pu) }}" method="POST" class="flex gap-2 items-end">
                    @csrf
                    <input type="hidden" name="requested_role" value="{{ $pu->role }}">

                    <label class="flex-1">
                        <span class="block text-[10px] font-bold mb-1">الدور عند القبول</span>
                        <select name="role"
                                class="w-full border-2 border-unicef-dark px-2 py-2 text-xs bg-white focus:outline-none focus:bg-unicef-light">
                            @foreach($roles as $r)
                                <option value="{{ $r->slug }}" @selected($r->slug === $pu->role)>
                                    {{ $r->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <button class="bg-unicef-green text-white border-2 border-unicef-dark px-4 py-2 text-xs font-bold">
                        اعتماد
                    </button>
                </form>

                <form action="{{ route('admin.facilitators.reject', $pu) }}" method="POST" class="sm:col-span-2">
                    @csrf @method('PATCH')
                    <button class="bg-red-600 text-white border-2 border-unicef-dark px-4 py-2 text-xs font-bold">
                        رفض الطلب
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </section>
    @php endif; @endphp

    <!-- قسم إدارة واعتماد قائمة الميسرين (لدور users.view) -->
    @php if ($can('users.view')): @endphp
    <section id="facilitators-section" class="space-y-4">
        @if(session('new_user_credentials'))
        <div class="p-5 border-2 border-unicef-dark bg-unicef-dark text-white text-xs space-y-3 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-neutral-700 pb-2 gap-2">
                <div class="font-black text-sm text-white flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-white text-unicef-dark font-mono font-bold">[ 🔑 حساب جديد مسجل ]</span>
                    <span>بيانات الدخول المعتمدة للمستخدم المسجل حديثاً</span>
                </div>
                <span class="text-[11px] text-neutral-300 font-bold underline">انسخ وسلم هذه البيانات للميسر فوراً:</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 font-mono text-xs">
                <div class="bg-unicef-darkBlue p-3 border border-neutral-700">
                    <div class="text-[10px] text-neutral-400 uppercase font-sans">الاسم واللقب</div>
                    <div class="font-bold text-white mt-1">{{ session('new_user_credentials')['name'] }}</div>
                </div>
                <div class="bg-unicef-darkBlue p-3 border border-neutral-700">
                    <div class="text-[10px] text-neutral-400 uppercase font-sans">البريد الإلكتروني</div>
                    <div class="font-bold text-white mt-1 select-all">{{ session('new_user_credentials')['email'] }}</div>
                </div>
                <div class="bg-unicef-darkBlue p-3 border border-neutral-700">
                    <div class="text-[10px] text-neutral-400 uppercase font-sans">الصفة / الدور</div>
                    <div class="font-bold text-white mt-1">{{ session('new_user_credentials')['role'] }}</div>
                </div>
                <div class="bg-white p-3 border-2 border-white text-unicef-dark">
                    <div class="text-[10px] text-neutral-600 uppercase font-sans font-bold">كلمة المرور المؤقتة</div>
                    <div class="font-black text-base mt-0.5 select-all font-mono">{{ session('new_user_credentials')['password'] }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-unicef-dark pb-3">
                <div>
                    <h2 class="font-black text-base text-unicef-dark uppercase">
                        [ 👥 ] إدارة واعتماد قائمة الميسرين والمساعدين الميدانيين
                    </h2>
                    <p class="text-[11px] text-neutral-600 mt-0.5">
                        إدخال وتعديل بيانات الميسرين المعتمدين لتظهر تلقائياً في استمارات الجلسات والتقارير الرسمية.
                    </p>
                </div>
                <span class="border border-unicef-dark px-2.5 py-1 text-xs font-bold bg-unicef-dark text-white font-mono self-start sm:self-auto">
                    {{ $allUsers->count() }} ميسر معتمد
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @php if ($can('users.manage')): @endphp
                <!-- نموذج إدخال ميسر جديد -->
                <div class="lg:col-span-1 bg-white border-2 border-unicef-dark p-5 shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] space-y-3"
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
                    <div class="border-b-2 border-unicef-dark pb-2 flex items-center justify-between">
                        <h3 class="font-black text-xs uppercase text-unicef-dark">[ + ] تسجيل ميسر / مستخدم جديد</h3>
                        <span class="text-[10px] text-neutral-500 font-mono">حصر للإدارة</span>
                    </div>

                    <form action="{{ route('admin.facilitators.store') }}" method="POST" class="space-y-3 text-xs">
                        @csrf

                        <div>
                            <label class="block font-bold text-unicef-dark mb-1">الاسم واللقب *</label>
                            <input type="text" name="name" required placeholder="مثال: يوسف العربي" class="w-full p-2 text-xs border-2 border-unicef-dark">
                        </div>

                        <div>
                            <label class="block font-bold text-unicef-dark mb-1">البريد الإلكتروني *</label>
                            <input type="email" name="email" required placeholder="youssef@example.com" class="w-full p-2 text-xs font-mono border-2 border-unicef-dark">
                        </div>

                        <div>
                            <label class="block font-bold text-unicef-dark mb-1">الصفة / الصلاحية *</label>
                            <select name="role" required class="w-full p-2 text-xs font-bold border-2 border-unicef-dark">
                                <option value="facilitator">ميسر رئيسي (Lead Facilitator) - يدير جلسات</option>
                                <option value="assistant">ميسر مساعد (Assistant) - مشارك ميداني</option>
                                <option value="admin">مسؤول نظام (Admin) - تحكم مركزي كامل</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-unicef-dark mb-1">رقم الهاتف</label>
                            <input type="text" name="phone" placeholder="0661000000" class="w-full p-2 text-xs font-mono border-2 border-unicef-dark">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-bold text-unicef-dark mb-1">الولاية</label>
                                <input type="text" name="wilaya" placeholder="الجزائر" class="w-full p-2 text-xs border-2 border-unicef-dark">
                            </div>
                            <div>
                                <label class="block font-bold text-unicef-dark mb-1">البلدية</label>
                                <input type="text" name="municipality" placeholder="بولوغين" class="w-full p-2 text-xs border-2 border-unicef-dark">
                            </div>
                        </div>

                        <div class="border-t border-unicef-dark pt-2 space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="font-bold text-unicef-dark">كلمة المرور (اختياري)</label>
                                <button type="button" @click="generatePassword()" class="text-[10px] font-mono font-bold text-unicef-dark underline hover:text-unicef-dark">
                                    [ 🎲 توليد كلمة مرور ]
                                </button>
                            </div>
                            <input type="text" name="password" x-model="regPassword" placeholder="اتركه فارغاً لتوليدها تلقائياً" class="w-full p-2 text-xs font-mono border-2 border-unicef-dark">
                            <p class="text-[10px] text-neutral-500">في حال تركه فارغاً، سيولد النظام كلمة مرور عشوائية آمنة تلقائياً.</p>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-unicef-dark text-white hover:bg-unicef-darkBlue font-black text-xs uppercase tracking-wider border border-unicef-dark transition">
                            + حفظ وإدراج المستخدم في القائمة
                        </button>
                    </form>
                </div>
                @php endif; @endphp

                <!-- جدول استعراض الميسرين -->
                <div class="{{ $can('users.manage') ? 'lg:col-span-2' : 'lg:col-span-3' }} bg-white border-2 border-unicef-dark shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] overflow-hidden">
                    <div class="p-3 bg-unicef-dark text-white flex items-center justify-between border-b-2 border-unicef-dark text-xs font-bold">
                        <span>قائمة الميسرين المعتمدين في النظام</span>
                        <span class="font-mono text-[11px] text-neutral-300">يظهرون فورياً في قوائم الجلسات والتقارير</span>
                    </div>

                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-neutral-100 border-b-2 border-unicef-dark font-bold sticky top-0">
                                <tr>
                                    <th class="py-2.5 px-3">الاسم واللقب</th>
                                    <th class="py-2.5 px-3">الصفة</th>
                                    <th class="py-2.5 px-3">الحالة</th>
                                    <th class="py-2.5 px-3">الهاتف</th>
                                    <th class="py-2.5 px-3">الموقع</th>
                                    <th class="py-2.5 px-3 text-center">الجلسات</th>
                                    @php if ($can('users.manage')): @endphp
                                    <th class="py-2.5 px-3 text-center">الإجراءات</th>
                                    @php endif; @endphp
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-unicef-dark font-medium">
                                @forelse($allUsers as $f)
                                <tr class="hover:bg-neutral-50 transition {{ !$f->is_active ? 'opacity-60 bg-neutral-100' : '' }}">
                                    <td class="py-2.5 px-3 font-bold text-unicef-dark">
                                        <div>{{ $f->name }}</div>
                                        <div class="text-[10px] text-neutral-500 font-mono">{{ $f->email }}</div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($f->role === 'admin')
                                            <span class="border border-unicef-dark px-1.5 py-0.5 bg-unicef-dark text-white text-[10px] font-bold">مدير</span>
                                        @elseif($f->role === 'facilitator')
                                            <span class="border border-unicef-dark px-1.5 py-0.5 bg-white text-unicef-dark text-[10px] font-bold">ميسر رئيسي</span>
                                        @else
                                            <span class="border border-unicef-dark px-1.5 py-0.5 bg-neutral-200 text-unicef-dark text-[10px] font-bold">مساعد</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($f->is_active)
                                            <span class="border border-unicef-dark px-1.5 py-0.5 bg-unicef-dark text-white text-[10px] font-bold">✓ مفعل</span>
                                        @else
                                            <span class="border border-unicef-dark px-1.5 py-0.5 bg-neutral-300 text-unicef-dark text-[10px] font-bold">⏸ معطل</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 font-mono">{{ $f->phone ?? '—' }}</td>
                                    <td class="py-2.5 px-3">
                                        {{ $f->municipality ? $f->municipality . ' (' . $f->wilaya . ')' : '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold">
                                        {{ $f->lead_sessions_count }}
                                    </td>
                                    @php if ($can('users.manage')): @endphp
                                    <td class="py-2.5 px-3 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button"
                                                @click="openFacEdit({{ Illuminate\Support\Js::from(['action' => route('admin.facilitators.update', $f), 'name' => $f->name, 'email' => $f->email, 'role' => $f->role, 'phone' => $f->phone, 'wilaya' => $f->wilaya, 'municipality' => $f->municipality]) }})"
                                                class="border border-unicef-dark px-2 py-0.5 text-[10px] font-bold bg-white text-unicef-dark hover:bg-unicef-dark hover:text-white transition">
                                                ✏️ تعديل
                                            </button>
                                            @if($f->id !== Auth::id())
                                            <form action="{{ route('admin.facilitators.toggleStatus', $f) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="border border-unicef-dark px-1.5 py-0.5 text-[10px] font-bold hover:bg-unicef-dark hover:text-white transition" title="{{ $f->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                                                    {{ $f->is_active ? '⏸' : '▶️' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.facilitators.destroy', $f) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد حذف هذا الميسر؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="border border-unicef-dark px-1.5 py-0.5 text-[10px] font-bold hover:bg-unicef-dark hover:text-white transition" title="حذف الميسر">
                                                    🗑️
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-neutral-400 text-[10px]">(أنت)</span>
@php endif; @endphp
                                        </div>
                                    </td>
                                    @php endif; @endphp
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $can('users.manage') ? 7 : 6 }}" class="py-6 text-center text-neutral-400">لا يوجد ميسرون مسجلون.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @php endif; @endphp

    <!-- الجلسات بانتظار الاعتماد (لدور sessions.approve) -->
    @php if ($can('sessions.approve') && isset($pendingApprovals) && $pendingApprovals->count() > 0): @endphp
    <section class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-4">
        <div class="flex items-center justify-between border-b-2 border-unicef-dark pb-2">
            <h2 class="font-black text-unicef-dark text-sm uppercase">
                [ ! ] جلسات وتقارير نشاط بانتظار الاعتماد والمصادقة الإدارية
            </h2>
            <span class="border border-unicef-dark px-2 py-0.5 text-xs font-bold bg-unicef-dark text-white font-mono">
                {{ $pendingApprovals->count() }} بانتظار القرار
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($pendingApprovals as $pending)
            <div class="bg-white p-4 border border-unicef-dark flex flex-col justify-between space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-sm text-unicef-dark">{{ $pending->title }}</span>
                        <span class="text-xs font-mono">{{ $pending->session_date->format('Y/m/d') }}</span>
                    </div>
                    <div class="text-xs text-neutral-600 space-y-0.5">
                        <div>المؤسسة: <strong>{{ $pending->institution->name }}</strong> (بلدية {{ $pending->municipality }})</div>
                        <div>الميسر المقرر: <strong>{{ $pending->leadFacilitator1->name ?? 'غير محدد' }}</strong></div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-unicef-dark">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.sessions.edit', $pending) }}" class="text-xs font-bold text-unicef-dark underline">✏️ تعديل الجلسة</a>
                        <span>|</span>
                        <a href="{{ route('portal.sessions.report', $pending) }}" class="text-xs font-bold text-unicef-dark underline">مراجعة التقرير &larr;</a>
                        <span>|</span>
                        <a href="{{ route('sessions.print.report', $pending) }}" target="_blank" class="text-xs font-bold text-neutral-600 underline">معاينة للطباعة</a>
                    </div>

                    <form action="{{ route('admin.sessions.approve', $pending) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-unicef-dark text-white hover:bg-unicef-darkBlue text-xs font-bold border border-unicef-dark transition">
                            [ ✓ ] اعتماد ومصادقة
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @php endif; @endphp

    <!-- شبكة الأنشطة الميدانية (لكل المستخدمين المسجلين) -->
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-black text-unicef-dark flex items-center gap-2">
                <span>📋</span>
                <span>قائمة الأنشطة والورشات الميدانية (مرتبة تنازلياً: الأحدث أولاً)</span>
            </h2>
            <span class="text-xs font-semibold text-slate-400">
                إجمالي الأنشطة: {{ $sessions->count() }}
            </span>
        </div>

        @if($search)
        <div class="bg-unicef-light border-2 border-unicef-dark p-3 text-xs font-bold text-unicef-dark">
            نتائج البحث عن: "<strong>{{ $search }}</strong>" — {{ $sessions->total() }} نتيجة
        </div>
        @endif

        <div class="space-y-4">
            @forelse($sessions as $session)
            @php
                $sessionJson = json_encode([
                    'id' => $session->id,
                    'title' => $session->title,
                    'date' => $session->session_date->format('Y-m-d'),
                    'date_formatted' => $session->session_date->format('Y/m/d'),
                    'status' => $session->status,
                    'institution' => $session->institution->name ?? 'غير محدد',
                    'wilaya' => $session->wilaya,
                    'municipality' => $session->municipality,
                    'venue' => $session->venue ?: 'مقر المؤسسة',
                    'lead1' => $session->leadFacilitator1->name ?? '—',
                    'lead2' => $session->leadFacilitator2->name ?? '—',
                    'assistants' => $session->assistant_facilitators ?? [],
                    'transit_from' => $session->transit_from_municipality ?: $session->municipality,
                    'transit_to' => $session->transit_to_municipality ?: $session->municipality,
                    'attendees_count' => $session->attendances->count(),
                    'attendance_url' => route('portal.sessions.attendance', $session),
                    'report_url' => route('portal.sessions.report', $session),
                    'edit_url' => route('portal.sessions.edit', $session),
                    'print_attendance_url' => route('sessions.print.attendance', $session),
                    'print_report_url' => route('sessions.print.report', $session),
                ], JSON_UNESCAPED_UNICODE);
            @endphp

            <div class="bg-white border-2 border-unicef-dark p-5 flex flex-col justify-between space-y-4 shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] border-t-4 {{ $session->status === 'approved' ? 'border-t-unicef-blue' : ($session->status === 'completed' ? 'border-t-emerald-500' : 'border-t-amber-400') }}">
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-unicef-dark">
                        <span class="font-mono font-bold text-neutral-500 flex items-center gap-1.5">
                            <span>📅</span>
                            <span>{{ $session->session_date->format('Y/m/d') }}</span>
                        </span>

                        @if($session->status === 'approved')
                            <span class="px-2.5 py-0.5 text-[11px] font-bold bg-unicef-light text-unicef-blue border border-unicef-blue flex items-center gap-1">
                                <span>✓</span>
                                <span>معتمد رسمياً</span>
                            </span>
                        @elseif($session->status === 'completed')
                            <span class="px-2.5 py-0.5 text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                مكتمل
                            </span>
                        @elseif($session->status === 'ongoing')
                            <span class="px-2.5 py-0.5 text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                جارية ميدانياً
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                مسودة أولية
                            </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-black text-sm text-unicef-dark leading-snug">{{ $session->title }}</h3>
                        <div class="text-xs text-neutral-600 mt-2 space-y-1">
                            <div class="flex items-center gap-1">
                                <span class="text-neutral-400">🏛️</span>
                                <span>المؤسسة:</span>
                                <strong class="text-unicef-dark">{{ $session->institution->name ?? 'غير محدد' }}</strong>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-neutral-400">📍</span>
                                <span>الموقع:</span>
                                <strong class="text-unicef-dark">بلدية {{ $session->municipality }} ({{ $session->wilaya }})</strong>
                            </div>
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-50 border border-unicef-dark flex items-center justify-between text-xs font-bold">
                        <span class="text-neutral-600 flex items-center gap-1">
                            <span>👥</span>
                            <span>حضور اليافعين:</span>
                        </span>
                        <span class="font-mono text-unicef-blue bg-white px-2 py-0.5 border border-unicef-dark">
                            {{ $session->attendances->count() }} مسجل
                        </span>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="space-y-2 pt-3 border-t border-unicef-dark">
                    <div class="grid grid-cols-3 gap-2">
                        @php if ($can('reports.view')): @endphp
                        <button type="button" @click="openPreview({{ $sessionJson }})"
                            class="py-2 px-2.5 bg-slate-100 hover:bg-unicef-light text-unicef-dark hover:text-unicef-blue font-bold text-xs transition flex items-center justify-center gap-1 border border-unicef-dark">
                            <span>👁️</span>
                            <span>معاينة</span>
                        </button>
                        @php endif; @endphp

                        @php if ($can('sessions.edit')): @endphp
                        <a href="{{ route('portal.sessions.edit', $session) }}"
                            class="py-2 px-2.5 bg-slate-100 hover:bg-unicef-dark hover:text-white text-unicef-dark font-bold text-xs transition flex items-center justify-center gap-1 border border-unicef-dark">
                            <span>✏️</span>
                            <span>تعديل</span>
                        </a>
                        @else
                        <div class="py-2 px-2.5 bg-slate-100 text-neutral-400 text-xs flex items-center justify-center gap-1 border border-unicef-dark opacity-50">
                            <span>✏️</span>
                            <span>تعديل</span>
                        </div>
                        @php endif; @endphp

                        @php if ($can('sessions.delete')): @endphp
                        <form action="{{ route('portal.sessions.destroy', $session) }}" method="POST"
                            onsubmit="return confirm('هل تريد بالتأكيد حذف النشاط ({{ $session->title }}) نهائياً؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full py-2 px-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition flex items-center justify-center gap-1 border border-unicef-dark">
                                <span>🗑️</span>
                                <span>حذف</span>
                            </button>
                        </form>
                        @else
                        <div class="w-full py-2 px-2 bg-red-50 text-red-300 text-xs flex items-center justify-center gap-1 border border-unicef-dark opacity-50">
                            <span>🗑️</span>
                            <span>حذف</span>
                        </div>
                        @php endif; @endphp
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        @php
    if ($can('attendance.view')) {
        echo '<a href="' . route('portal.sessions.attendance', $session) . '" class="py-2 px-3 bg-unicef-blue hover:bg-[#0E9BD3] text-white text-center font-bold text-xs transition flex items-center justify-center gap-1 border border-unicef-dark"><span>📋 كشف الحضور</span><span>&larr;</span></a>';
    } else {
        echo '<div class="py-2 px-3 bg-unicef-blue/20 text-unicef-blue/50 text-center text-xs flex items-center justify-center gap-1 border border-unicef-dark opacity-50"><span>📋 كشف الحضور</span><span>&larr;</span></div>';
    }
@endphp

                        @php
    if ($can('reports.view')) {
        echo '<a href="' . route('portal.sessions.report', $session) . '" class="py-2 px-3 bg-unicef-dark hover:bg-unicef-darkBlue text-white text-center font-bold text-xs transition flex items-center justify-center gap-1 border border-unicef-dark"><span>📝 تقرير النشاط</span><span>&larr;</span></a>';
    } else {
        echo '<div class="py-2 px-3 bg-unicef-dark/10 text-unicef-dark/50 text-center text-xs flex items-center justify-center gap-1 border border-unicef-dark opacity-50"><span>📝 تقرير النشاط</span><span>&larr;</span></div>';
    }
@endphp
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white border-2 border-unicef-dark p-12 text-center text-neutral-500 shadow-[4px_4px_0px_0px_rgba(0,47,108,1)] space-y-3">
                <div class="w-16 h-16 bg-unicef-dark text-white flex items-center justify-center mx-auto text-2xl border border-unicef-dark">
                    📋
                </div>
                <div class="font-black text-lg text-unicef-dark">لا توجد أنشطة ميدانية مسجلة حتى الآن</div>
                @php if ($can('sessions.create')): @endphp
                <button type="button" @click="resetModal(); showCreateModal = true"
                    class="px-6 py-2.5 bg-unicef-dark text-white hover:bg-unicef-darkBlue font-bold text-xs border border-unicef-dark transition">
                    + إضافة نشاط جديد الآن
                </button>
                @else
                <p class="text-xs text-neutral-400">لا تملك صلاحية إنشاء أنشطة جديدة. تواصل مع مدير النظام.</p>
                @php endif; @endphp
            </div>
            @endforelse
        </div>

        {{ $sessions->links() }}
    </section>

    <!-- النافذة المنبثقة متعددة الخطوات (Multi-Step Form Modal) -->
    @php if ($can('sessions.create')): @endphp
    <div x-show="showCreateModal" class="fixed inset-0 z-50 bg-unicef-dark/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border-4 border-unicef-dark shadow-[8px_8px_0px_0px_rgba(0,47,108,1)] max-w-2xl w-full max-h-[92vh] overflow-y-auto space-y-5 p-6 sm:p-8" @click.away="showCreateModal = false">
            
            <div class="space-y-4 border-b border-unicef-dark pb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-unicef-light text-unicef-blue flex items-center justify-center font-bold border border-unicef-dark">✨</span>
                        <div>
                            <h3 class="font-black text-base text-unicef-dark">إضافة نشاط ميداني جديد (Kit Adolescent)</h3>
                            <p class="text-[11px] text-neutral-500">نموذج متدرج متعدد الخطوات</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="text-unicef-dark font-black text-base border border-unicef-dark px-2 py-0.5 hover:bg-unicef-dark hover:text-white">✕</button>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold pt-1">
                    <div class="p-2 transition flex items-center justify-center gap-2" :class="step === 1 ? 'bg-unicef-dark text-white shadow-sm' : (step > 1 ? 'bg-unicef-light text-unicef-blue' : 'bg-neutral-200 text-neutral-400')">
                        <span class="w-5 h-5 border border-unicef-dark flex items-center justify-center text-[10px]" :class="step === 1 ? 'bg-white text-unicef-dark' : 'bg-unicef-dark text-white'">1</span>
                        <span>بيانات النشاط والموقع</span>
                    </div>
                    <div class="p-2 transition flex items-center justify-center gap-2" :class="step === 2 ? 'bg-unicef-dark text-white shadow-sm' : (step > 2 ? 'bg-unicef-light text-unicef-blue' : 'bg-neutral-200 text-neutral-400')">
                        <span class="w-5 h-5 border border-unicef-dark flex items-center justify-center text-[10px]" :class="step === 2 ? 'bg-white text-unicef-dark' : (step > 2 ? 'bg-unicef-dark text-white' : 'bg-neutral-300 text-white')">2</span>
                        <span>طاقم التنشيط والمسار</span>
                    </div>
                    <div class="p-2 transition flex items-center justify-center gap-2" :class="step === 3 ? 'bg-unicef-dark text-white shadow-sm' : 'bg-neutral-200 text-neutral-400'">
                        <span class="w-5 h-5 border border-unicef-dark flex items-center justify-center text-[10px]" :class="step === 3 ? 'bg-white text-unicef-dark' : 'bg-neutral-300 text-white'">3</span>
                        <span>المراجعة والتأكيد</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('portal.sessions.store') }}" method="POST" class="space-y-5 text-xs">
                @csrf

                <div x-show="step === 1" class="space-y-4">
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1.5">عنوان التدريب / النشاط *</label>
                        <input type="text" name="title" x-model="sessionTitle" required
                            class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-unicef-dark placeholder-neutral-400 focus:outline-none focus:bg-unicef-light transition font-bold"
                            placeholder="مثال: ورشة التعبير الفني والمهارات الحياتية">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-unicef-dark mb-1.5">تاريخ إجراء النشاط *</label>
                            <input type="date" name="session_date" x-model="sessionDate" required
                                class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs font-mono text-unicef-dark focus:outline-none focus:bg-unicef-light transition">
                        </div>

                        <div>
                            <label class="block font-bold text-unicef-dark mb-1.5">المؤسسة / الهيئة المستقبلة *</label>
                            <input type="text" name="institution_name" x-model="institutionName" required list="institutions-datalist"
                                class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-unicef-dark placeholder-neutral-400 focus:outline-none focus:bg-unicef-light transition font-medium"
                                placeholder="اكتب اسم المؤسسة / الهيئة المستقبلة مباشرة">
                            <datalist id="institutions-datalist">
                                @foreach($institutions as $inst)
                                    <option value="{{ $inst->name }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-unicef-dark mb-1.5">الولاية *</label>
                            <select name="wilaya" x-model="selectedWilaya" @change="onWilayaChange()" required
                                class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-unicef-dark focus:outline-none focus:bg-unicef-light transition bg-white">
                                <template x-for="(munics, wName) in wilayasData" :key="wName">
                                    <option :value="wName" x-text="wName" :selected="wName === selectedWilaya"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-unicef-dark mb-1.5">البلدية *</label>
                            <select name="municipality" x-model="selectedMunicipality" required
                                class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-unicef-dark focus:outline-none focus:bg-unicef-light transition bg-white">
                                <template x-for="m in (wilayasData[selectedWilaya] || [])" :key="m">
                                    <option :value="m" x-text="m" :selected="m === selectedMunicipality"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1.5">المقر / القاعة المحددة داخل المؤسسة</label>
                        <input type="text" name="venue" x-model="venue"
                            class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-unicef-dark placeholder-neutral-400 focus:outline-none focus:bg-unicef-light transition"
                            placeholder="مثال: قاعة الورشات، المكتبة، الفضاء المفتوح...">
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-unicef-dark">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-neutral-500 hover:text-unicef-dark font-bold border border-unicef-dark">إلغاء</button>
                        <button type="button" @click="step = 2"
                            :disabled="!sessionTitle || !institutionName || !selectedWilaya || !selectedMunicipality"
                            class="px-6 py-2.5 bg-unicef-dark text-white hover:bg-unicef-darkBlue disabled:opacity-50 font-bold transition flex items-center gap-1.5 shadow-sm border border-unicef-dark">
                            <span>التالي: طاقم التنشيط والمسار</span>
                            <span>&larr;</span>
                        </button>
                    </div>
                </div>

                <div x-show="step === 2" class="space-y-4" style="display: none;">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-unicef-dark mb-1.5">الميسر الرئيسي 1 *</label>
                            <select name="lead_facilitator_1_id" x-model="lead1" required
                                class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-unicef-dark focus:outline-none focus:bg-unicef-light transition bg-white">
                                <option value="">— اختر الميسر الأول —</option>
                                @foreach($facilitators as $f)
                                    <option value="{{ $f->id }}">{{ $f->name }} @if(Auth::id() == $f->id) (أنت) @endif</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-unicef-dark mb-1.5">الميسر الرئيسي 2 (اختياري)</label>
                            <select name="lead_facilitator_2_id" x-model="lead2"
                                class="w-full px-3.5 py-2.5 border-2 border-unicef-dark text-xs text-unicef-dark focus:outline-none focus:bg-unicef-light transition bg-white">
                                <option value="">— اختر الميسر الثاني —</option>
                                @foreach($facilitators as $f)
                                    <option value="{{ $f->id }}" :disabled="lead1 == '{{ $f->id }}'">{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block font-bold text-unicef-dark">الميسرون المساعدون (قوائم منسدلة 1 إلى 6):</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            @for($i = 0; $i < 6; $i++)
                            <div>
                                <label class="block text-[10px] text-neutral-500 mb-1">مساعد {{ $i + 1 }}:</label>
                                <select name="assistant_facilitators[]" x-model="assistants[{{ $i }}]"
                                    class="w-full p-2 border-2 border-unicef-dark text-xs text-unicef-dark bg-white">
                                    <option value="">— غير محدد —</option>
                                    @foreach($facilitators as $f)
                                        <option value="{{ $f->name }}"
                                            :disabled="lead1 == '{{ $f->id }}' || lead2 == '{{ $f->id }}' || assistants.some((a, idx) => idx !== {{ $i }} && a === @js($f->name))">
                                            {{ $f->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endfor
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 border border-unicef-dark space-y-2">
                        <div class="font-bold text-unicef-dark">مسار الانتقال الميداني:</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] text-neutral-500 mb-1">من بلدية:</label>
                                <input type="text" name="transit_from_municipality" x-model="transitFrom"
                                    class="w-full px-3 py-2 border-2 border-unicef-dark text-xs"
                                    placeholder="بلدية الانطلاق">
                            </div>
                            <div>
                                <label class="block text-[10px] text-neutral-500 mb-1">إلى بلدية:</label>
                                <input type="text" name="transit_to_municipality" x-model="transitTo"
                                    class="w-full px-3 py-2 border-2 border-unicef-dark text-xs"
                                    placeholder="بلدية المقر">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-unicef-dark">
                        <button type="button" @click="step = 1" class="px-4 py-2 text-neutral-600 font-bold hover:text-unicef-dark border border-unicef-dark">&rarr; السابق</button>
                        <button type="button" @click="step = 3"
                            :disabled="!lead1"
                            class="px-6 py-2.5 bg-unicef-dark text-white hover:bg-unicef-darkBlue disabled:opacity-50 font-bold transition flex items-center gap-1.5 shadow-sm border border-unicef-dark">
                            <span>التالي: المراجعة والتأكيد</span>
                            <span>&larr;</span>
                        </button>
                    </div>
                </div>

                <div x-show="step === 3" class="space-y-4" style="display: none;">
                    <div class="p-4 bg-unicef-light border border-unicef-blue space-y-3">
                        <div class="font-black text-unicef-dark text-sm border-b border-unicef-blue/20 pb-2">
                            ملخص بيانات النشاط قبل الحفظ:
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-neutral-500">عنوان النشاط:</span>
                                <strong class="block text-unicef-dark" x-text="sessionTitle"></strong>
                            </div>
                            <div>
                                <span class="text-neutral-500">التاريخ المقرر:</span>
                                <strong class="block text-unicef-dark font-mono" x-text="sessionDate"></strong>
                            </div>
                            <div>
                                <span class="text-neutral-500">المؤسسة المستقبلة:</span>
                                <strong class="block text-unicef-dark" x-text="institutionName || 'غير محدد'"></strong>
                            </div>
                            <div>
                                <span class="text-neutral-500">الموقع:</span>
                                <strong class="block text-unicef-dark" x-text="selectedMunicipality + ' (' + selectedWilaya + ')'"></strong>
                            </div>
                            <div>
                                <span class="text-neutral-500">الميسر المسؤول:</span>
                                <strong class="block text-unicef-dark">{{ Auth::user()->name }}</strong>
                            </div>
                            <div>
                                <span class="text-neutral-500">المقر الدقيق:</span>
                                <strong class="block text-unicef-dark" x-text="venue || 'مقر المؤسسة'"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-emerald-50 border border-emerald-600 text-emerald-800 text-[11px] flex items-center gap-2">
                        <span class="text-emerald-600 font-bold text-sm">✓</span>
                        <span>بمجرد الحفظ، سيتم تحويلك مباشرة إلى شاشة كشف الحضور لتسجيل اليافعين وتأكيد الحضور الميداني.</span>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-unicef-dark">
                        <button type="button" @click="step = 2" class="px-4 py-2 text-neutral-600 font-bold hover:text-unicef-dark border border-unicef-dark">&rarr; السابق</button>
                        <button type="submit"
                            :disabled="!sessionTitle || !institutionName || !selectedWilaya || !selectedMunicipality || !lead1"
                            class="px-7 py-3 bg-unicef-dark text-white hover:bg-unicef-darkBlue disabled:opacity-50 disabled:cursor-not-allowed font-black text-xs uppercase tracking-wider transition shadow-sm border border-unicef-dark flex items-center gap-2">
                            <span>[ ✓ ] حفظ وبدء تسجيل كشف الحضور</span>
                            <span>&larr;</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @php endif; @endphp

    <!-- النافذة المنبثقة لمعاينة النشاط -->
    @php if ($can('reports.view')): @endphp
    <div x-show="showPreviewModal && previewSession" class="fixed inset-0 z-50 bg-unicef-dark/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border-4 border-unicef-dark shadow-[8px_8px_0px_0px_rgba(0,47,108,1)] max-w-xl w-full max-h-[92vh] overflow-y-auto space-y-5 p-6" @click.away="showPreviewModal = false">
            
            <div class="flex items-center justify-between border-b border-unicef-dark pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 bg-unicef-light text-unicef-blue flex items-center justify-center font-bold border border-unicef-dark">👁️</span>
                    <div>
                        <h3 class="font-black text-base text-unicef-dark" x-text="previewSession?.title"></h3>
                        <p class="text-[11px] text-neutral-500 font-mono" x-text="'تاريخ الإجراء: ' + previewSession?.date_formatted"></p>
                    </div>
                </div>
                <button type="button" @click="showPreviewModal = false" class="text-unicef-dark font-black text-base border border-unicef-dark px-2 py-0.5 hover:bg-unicef-dark hover:text-white">✕</button>
            </div>

            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-50 border border-unicef-dark">
                    <div>
                        <span class="text-neutral-400 block text-[10px]">المؤسسة المستقبلة:</span>
                        <strong class="text-unicef-dark" x-text="previewSession?.institution"></strong>
                    </div>
                    <div>
                        <span class="text-neutral-400 block text-[10px]">الموقع الجغرافي:</span>
                        <strong class="text-unicef-dark" x-text="previewSession?.municipality + ' (' + previewSession?.wilaya + ')'"></strong>
                    </div>
                    <div>
                        <span class="text-neutral-400 block text-[10px]">الميسر الرئيسي:</span>
                        <strong class="text-unicef-dark" x-text="previewSession?.lead1"></strong>
                    </div>
                    <div>
                        <span class="text-neutral-400 block text-[10px]">المسجلون بالحضور:</span>
                        <strong class="text-unicef-blue font-mono" x-text="previewSession?.attendees_count + ' يافع'"></strong>
                    </div>
                    <div class="col-span-2">
                        <span class="text-neutral-400 block text-[10px]">مسار الانتقال:</span>
                        <strong class="text-neutral-600" x-text="'من بلدية: ' + previewSession?.transit_from + ' إلى: ' + previewSession?.transit_to"></strong>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <div class="grid grid-cols-2 gap-2">
                        <a :href="previewSession?.attendance_url"
                            class="py-2.5 px-3 bg-unicef-blue text-white text-center font-bold hover:bg-[#0E9BD3] transition flex items-center justify-center gap-1 border border-unicef-dark">
                            <span>📋 كشف الحضور</span>
                        </a>
                        <a :href="previewSession?.report_url"
                            class="py-2.5 px-3 bg-unicef-dark text-white text-center font-bold hover:bg-unicef-darkBlue transition flex items-center justify-center gap-1 border border-unicef-dark">
                            <span>📝 تقرير النشاط</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-unicef-dark">
                        <a :href="previewSession?.print_attendance_url" target="_blank"
                            class="py-2 px-3 border border-unicef-dark text-unicef-dark text-center font-bold hover:bg-neutral-100 transition">
                            🖨️ طباعة كشف الحضور
                        </a>
                        <a :href="previewSession?.print_report_url" target="_blank"
                            class="py-2 px-3 border border-unicef-dark text-unicef-dark text-center font-bold hover:bg-neutral-100 transition">
                            📑 طباعة التقرير الشامل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php endif; @endphp

    <!-- النافذة المنبثقة لتعديل بيانات الميسر -->
    @php if ($can('users.manage')): @endphp
    <div x-show="showFacEditModal" class="fixed inset-0 z-50 bg-unicef-dark/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border-4 border-unicef-dark shadow-[8px_8px_0px_0px_rgba(0,47,108,1)] max-w-lg w-full max-h-[92vh] overflow-y-auto space-y-5 p-6" @click.away="showFacEditModal = false">

            <div class="flex items-center justify-between border-b border-unicef-dark pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 bg-unicef-light text-unicef-blue flex items-center justify-center font-bold border border-unicef-dark">✏️</span>
                    <h3 class="font-black text-base text-unicef-dark">تعديل بيانات الميسر</h3>
                </div>
                <button type="button" @click="showFacEditModal = false" class="text-unicef-dark font-black text-base border border-unicef-dark px-2 py-0.5 hover:bg-unicef-dark hover:text-white">✕</button>
            </div>

            <form :action="facEditAction" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">الاسم واللقب *</label>
                    <input type="text" name="name" x-model="facName" required class="w-full p-2 text-xs border-2 border-unicef-dark">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">البريد الإلكتروني *</label>
                    <input type="email" name="email" x-model="facEmail" required class="w-full p-2 text-xs font-mono border-2 border-unicef-dark">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">الصفة / الصلاحية *</label>
                    <select name="role" x-model="facRole" required class="w-full p-2 text-xs font-bold border-2 border-unicef-dark">
                        <option value="facilitator">ميسر رئيسي (Lead Facilitator) - يدير جلسات</option>
                        <option value="assistant">ميسر مساعد (Assistant) - مشارك ميداني</option>
                        <option value="admin">مسؤول نظام (Admin) - تحكم مركزي كامل</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">رقم الهاتف</label>
                    <input type="text" name="phone" x-model="facPhone" class="w-full p-2 text-xs font-mono border-2 border-unicef-dark">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">الولاية</label>
                        <input type="text" name="wilaya" x-model="facWilaya" class="w-full p-2 text-xs border-2 border-unicef-dark">
                    </div>
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">البلدية</label>
                        <input type="text" name="municipality" x-model="facMunicipality" class="w-full p-2 text-xs border-2 border-unicef-dark">
                    </div>
                </div>

                <div class="border-t border-unicef-dark pt-2">
                    <label class="block font-bold text-unicef-dark mb-1">كلمة المرور الجديدة (اختياري)</label>
                    <input type="text" name="password" x-model="facPassword" placeholder="اتركه فارغاً للإبقاء على كلمة المرور الحالية" class="w-full p-2 text-xs font-mono border-2 border-unicef-dark">
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button type="button" @click="showFacEditModal = false" class="py-2.5 border border-unicef-dark font-bold text-unicef-dark hover:bg-neutral-100 transition">إلغاء</button>
                    <button type="submit" class="py-2.5 bg-unicef-dark text-white font-black uppercase tracking-wider border border-unicef-dark hover:bg-unicef-darkBlue transition">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
    @php endif; @endphp

</div>
@endsection