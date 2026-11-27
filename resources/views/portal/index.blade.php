@extends('layouts.app')

@section('title', 'قائمة الأنشطة الميدانية للميسر')

@section('content')
<div class="space-y-6" 
     x-data="{
        searchQuery: '',
        statusFilter: 'all',
        showCreateModal: false,
        showPreviewModal: false,
        previewSession: null,

        // بيانات الخطوات في Multi-step modal
        step: 1,
        institutions: {{ $institutions->toJson() }},
        wilayasData: {{ json_encode($wilayasData, JSON_UNESCAPED_UNICODE) }},
        selectedInstId: '',
        selectedWilaya: 'الجزائر',
        selectedMunicipality: 'باب الوادي',
        lead1: '{{ Auth::id() }}',
        lead2: '',
        sessionTitle: 'تدريب حقيبة الابتكار والتعبير لليافعين Kit Adolescent',
        sessionDate: '{{ date('Y-m-d') }}',
        venue: '',
        transitFrom: '',
        transitTo: '',

        onInstitutionChange() {
            const inst = this.institutions.find(i => i.id == this.selectedInstId);
            if (inst) {
                if (inst.wilaya && this.wilayasData[inst.wilaya]) {
                    this.selectedWilaya = inst.wilaya;
                }
                if (inst.municipality) {
                    this.selectedMunicipality = inst.municipality;
                }
                if (!this.transitTo) {
                    this.transitTo = inst.municipality || '';
                }
            }
        },

        onWilayaChange() {
            const munics = this.wilayasData[this.selectedWilaya] || [];
            this.selectedMunicipality = munics.length > 0 ? munics[0] : '';
        },

        openPreview(s) {
            this.previewSession = s;
            this.showPreviewModal = true;
        },

        resetModal() {
            this.step = 1;
            this.selectedInstId = '';
            this.venue = '';
            this.transitFrom = '';
            this.transitTo = '';
        }
     }">

    <!-- ترويسة الواجهة بنمط اليونيسف الرسمي -->
    <div class="bg-gradient-to-r from-unicef-dark to-[#004B87] text-white p-6 sm:p-8 rounded-2xl shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6 border-b-4 border-unicef-blue">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-cyan-200 text-xs font-bold font-mono">
                <span class="w-2 h-2 rounded-full bg-unicef-blue animate-pulse"></span>
                <span>بوابة الميسر الميداني &bull; UNICEF M&E</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">أهلاً بك، {{ Auth::user()->name }}</h1>
            <p class="text-xs text-slate-200 max-w-xl leading-relaxed">
                قائمة الأنشطة والورشات الميدانية المسجلة باسمك مرتبة من الأحدث إلى الأقدم. يمكنك البحث الفوري، إضافة نشاط جديد عبر النموذج المتدرج، أو معاينة وتعديل وحذف الجلسات.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="resetModal(); showCreateModal = true"
                class="px-5 py-3 rounded-xl bg-unicef-blue hover:bg-[#0E9BD3] text-white font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-unicef-blue/30 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>إضافة نشاط جديد (Multi-step)</span>
            </button>
        </div>
    </div>

    <!-- شريط البحث السريع والفلترة بنمط اليونيسف الرسمي (UNICEF Material Search Bar) -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- صندوق البحث -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5 text-unicef-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="searchQuery"
                    class="w-full pr-11 pl-4 py-3 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition bg-slate-50/50"
                    placeholder="ابحث عن نشاط بالعنوان، اسم المؤسسة، البلدية، التاريخ، أو حالة النشاط...">
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 hover:text-slate-600">
                    ✕
                </button>
            </div>

            <!-- أزرار الفلترة بحسب الحالة -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-bold">
                <button type="button" @click="statusFilter = 'all'"
                    :class="statusFilter === 'all' ? 'bg-unicef-dark text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="px-3.5 py-2 rounded-xl transition whitespace-nowrap">
                    كافة الأنشطة ({{ count($mySessions) }})
                </button>
                <button type="button" @click="statusFilter = 'approved'"
                    :class="statusFilter === 'approved' ? 'bg-unicef-blue text-white' : 'bg-cyan-50 text-unicef-blue hover:bg-cyan-100'"
                    class="px-3.5 py-2 rounded-xl transition whitespace-nowrap">
                    معتمدة
                </button>
                <button type="button" @click="statusFilter = 'completed'"
                    :class="statusFilter === 'completed' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                    class="px-3.5 py-2 rounded-xl transition whitespace-nowrap">
                    مكتملة
                </button>
                <button type="button" @click="statusFilter = 'draft'"
                    :class="statusFilter === 'draft' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                    class="px-3.5 py-2 rounded-xl transition whitespace-nowrap">
                    مسودة أولية
                </button>
            </div>

        </div>
    </div>

    <!-- شبكة الأنشطة الميدانية مرتبة تنازلياً (النشاط الجديد هو الأول دائماً) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-black text-unicef-dark flex items-center gap-2">
                <span>📋</span>
                <span>قائمة الأنشطة والورشات الميدانية (مرتبة تنازلياً: الأحدث أولاً)</span>
            </h2>
            <span class="text-xs font-semibold text-slate-400">
                إجمالي الأنشطة: {{ count($mySessions) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($mySessions as $session)
                @php
                    // Blade {{ }} يقوم بالترميز تلقائياً، لذا يكفي تمرير JSON الخام
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

                <!-- بطاقة النشاط التفاعلية -->
                <div x-show="(statusFilter === 'all' || statusFilter === '{{ $session->status }}') &&
                             (searchQuery === '' || 
                              '{{ strtolower($session->title) }}'.includes(searchQuery.toLowerCase()) || 
                              '{{ strtolower($session->institution->name ?? '') }}'.includes(searchQuery.toLowerCase()) || 
                              '{{ strtolower($session->municipality) }}'.includes(searchQuery.toLowerCase()) || 
                              '{{ $session->session_date->format('Y-m-d') }}'.includes(searchQuery) || 
                              '{{ $session->status }}'.includes(searchQuery))"
                    class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col justify-between space-y-4 shadow-sm hover:shadow-md transition group border-t-4 {{ $session->status === 'approved' ? 'border-t-unicef-blue' : ($session->status === 'completed' ? 'border-t-emerald-500' : 'border-t-amber-400') }}">
                    
                    <div class="space-y-3">
                        <!-- شريط التاريخ والحالة -->
                        <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-100">
                            <span class="font-mono font-bold text-slate-500 flex items-center gap-1.5">
                                <span>📅</span>
                                <span>{{ $session->session_date->format('Y/m/d') }}</span>
                            </span>

                            @if($session->status === 'approved')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-50 text-unicef-blue border border-unicef-blue/30 flex items-center gap-1">
                                    <span>✓</span>
                                    <span>معتمد رسمياً</span>
                                </span>
                            @elseif($session->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    مكتمل
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    مسودة أولية
                                </span>
                            @endif
                        </div>

                        <!-- عنوان النشاط والموقع -->
                        <div>
                            <h3 class="font-black text-sm text-unicef-dark leading-snug group-hover:text-unicef-blue transition">
                                {{ $session->title }}
                            </h3>
                            <div class="text-xs text-slate-500 mt-2 space-y-1">
                                <div class="flex items-center gap-1">
                                    <span class="text-slate-400">🏛️</span>
                                    <span>المؤسسة:</span>
                                    <strong class="text-slate-700">{{ $session->institution->name ?? 'غير محدد' }}</strong>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-slate-400">📍</span>
                                    <span>الموقع:</span>
                                    <strong class="text-slate-700">بلدية {{ $session->municipality }} ({{ $session->wilaya }})</strong>
                                </div>
                            </div>
                        </div>

                        <!-- إحصائية الحضور -->
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-600 flex items-center gap-1">
                                <span>👥</span>
                                <span>حضور اليافعين:</span>
                            </span>
                            <span class="font-mono text-unicef-blue bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                {{ $session->attendances->count() }} مسجل
                            </span>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات الأساسية المطلوبة: معاينة + تعديل + حذف -->
                    <div class="space-y-2 pt-3 border-t border-slate-100">
                        <div class="grid grid-cols-3 gap-2">
                            <!-- 1. معاينة -->
                            <button type="button" @click="openPreview({{ $sessionJson }})"
                                class="py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-unicef-light text-unicef-dark hover:text-unicef-blue font-bold text-xs transition flex items-center justify-center gap-1">
                                <span>👁️</span>
                                <span>معاينة</span>
                            </button>

                            <!-- 2. تعديل -->
                            <a href="{{ route('portal.sessions.edit', $session) }}"
                                class="py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1">
                                <span>✏️</span>
                                <span>تعديل</span>
                            </a>

                            <!-- 3. حذف -->
                            <form action="{{ route('portal.sessions.destroy', $session) }}" method="POST"
                                onsubmit="return confirm('هل تريد بالتأكيد حذف النشاط ({{ $session->title }}) نهائياً؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-2 px-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition flex items-center justify-center gap-1">
                                    <span>🗑️</span>
                                    <span>حذف</span>
                                </button>
                            </form>
                        </div>

                        <!-- روابط سريعة لكشف الحضور والتقرير والطباعة -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <a href="{{ route('portal.sessions.attendance', $session) }}"
                                class="py-2 px-3 rounded-xl bg-unicef-blue hover:bg-[#0E9BD3] text-white text-center font-bold text-xs transition shadow-sm flex items-center justify-center gap-1">
                                <span>كشف الحضور</span>
                                <span>&larr;</span>
                            </a>
                            <a href="{{ route('portal.sessions.report', $session) }}"
                                class="py-2 px-3 rounded-xl bg-unicef-dark hover:bg-[#001D44] text-white text-center font-bold text-xs transition shadow-sm flex items-center justify-center gap-1">
                                <span>تقرير النشاط</span>
                                <span>&larr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500 shadow-sm space-y-3">
                    <div class="w-16 h-16 rounded-full bg-unicef-light text-unicef-blue flex items-center justify-center mx-auto text-2xl">
                        📋
                    </div>
                    <div class="font-black text-lg text-unicef-dark">لا توجد أنشطة ميدانية مسجلة حتى الآن</div>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        قم بالضغط على الزر أدناه لفتح استمارة النشاط متعددة الخطوات وإدخال أول ورشة ميدانية.
                    </p>
                    <button type="button" @click="resetModal(); showCreateModal = true"
                        class="px-6 py-2.5 rounded-xl bg-unicef-blue hover:bg-[#0E9BD3] text-white font-bold text-xs shadow-md transition">
                        + إضافة نشاط جديد الآن
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 1. النافذة المنبثقة متعددة الخطوات (Multi-Step Form Modal)      -->
    <!-- ============================================================== -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 bg-unicef-dark/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[92vh] overflow-y-auto border border-slate-200 space-y-5 p-6 sm:p-8"
             @click.away="showCreateModal = false">
            
            <!-- رأس النافذة والستيبار (Material Stepper) -->
            <div class="space-y-4 border-b border-slate-100 pb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-unicef-light text-unicef-blue flex items-center justify-center font-bold">✨</span>
                        <div>
                            <h3 class="font-black text-base text-unicef-dark">إضافة نشاط ميداني جديد (Kit Adolescent)</h3>
                            <p class="text-[11px] text-slate-400">نموذج متدرج متعدد الخطوات بنمط اليونيسف الرسمي</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>
                </div>

                <!-- شريط الخطوات (Stepper Indicator) -->
                <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold pt-1">
                    <div class="p-2 rounded-xl transition flex items-center justify-center gap-2"
                         :class="step === 1 ? 'bg-unicef-blue text-white shadow-sm' : (step > 1 ? 'bg-cyan-50 text-unicef-blue' : 'bg-slate-100 text-slate-400')">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="step === 1 ? 'bg-white text-unicef-blue' : 'bg-unicef-blue text-white'">1</span>
                        <span>بيانات النشاط والموقع</span>
                    </div>

                    <div class="p-2 rounded-xl transition flex items-center justify-center gap-2"
                         :class="step === 2 ? 'bg-unicef-blue text-white shadow-sm' : (step > 2 ? 'bg-cyan-50 text-unicef-blue' : 'bg-slate-100 text-slate-400')">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="step === 2 ? 'bg-white text-unicef-blue' : (step > 2 ? 'bg-unicef-blue text-white' : 'bg-slate-300 text-white')">2</span>
                        <span>طاقم التنشيط والمسار</span>
                    </div>

                    <div class="p-2 rounded-xl transition flex items-center justify-center gap-2"
                         :class="step === 3 ? 'bg-unicef-blue text-white shadow-sm' : 'bg-slate-100 text-slate-400'">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="step === 3 ? 'bg-white text-unicef-blue' : 'bg-slate-300 text-white'">3</span>
                        <span>المراجعة والتأكيد</span>
                    </div>
                </div>
            </div>

            <!-- النموذج متعدد الخطوات -->
            <form action="{{ route('portal.sessions.store') }}" method="POST" class="space-y-5 text-xs">
                @csrf

                <!-- الخطوة 1: بيانات النشاط والموقع -->
                <div x-show="step === 1" class="space-y-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">عنوان التدريب / النشاط *</label>
                        <input type="text" name="title" x-model="sessionTitle" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition font-bold"
                            placeholder="مثال: ورشة التعبير الفني والمهارات الحياتية">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">تاريخ إجراء النشاط *</label>
                            <input type="date" name="session_date" x-model="sessionDate" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono text-slate-800 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">المؤسسة المستقبلة *</label>
                            <select name="institution_id" x-model="selectedInstId" @change="onInstitutionChange()" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition bg-white font-medium">
                                <option value="">— اختر المؤسسة —</option>
                                @foreach($institutions as $inst)
                                    <option value="{{ $inst->id }}">{{ $inst->name }} ({{ $inst->municipality }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- الولاية والبلدية المترابطتان -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">الولاية *</label>
                            <select name="wilaya" x-model="selectedWilaya" @change="onWilayaChange()" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition bg-white">
                                <template x-for="(munics, wName) in wilayasData" :key="wName">
                                    <option :value="wName" x-text="wName" :selected="wName === selectedWilaya"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">البلدية *</label>
                            <select name="municipality" x-model="selectedMunicipality" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition bg-white">
                                <template x-for="m in (wilayasData[selectedWilaya] || [])" :key="m">
                                    <option :value="m" x-text="m" :selected="m === selectedMunicipality"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">المقر / القاعة المحددة داخل المؤسسة</label>
                        <input type="text" name="venue" x-model="venue"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition"
                            placeholder="مثال: قاعة الورشات، المكتبة، الفضاء المفتوح...">
                    </div>

                    <!-- أزرار التنقل -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-slate-500 hover:text-slate-800 font-bold">إلغاء</button>
                        <button type="button" @click="step = 2"
                            :disabled="!sessionTitle || !selectedInstId || !selectedWilaya || !selectedMunicipality"
                            class="px-6 py-2.5 rounded-xl bg-unicef-blue hover:bg-[#0E9BD3] disabled:opacity-50 text-white font-bold transition flex items-center gap-1.5 shadow-sm">
                            <span>التالي: طاقم التنشيط والمسار</span>
                            <span>&larr;</span>
                        </button>
                    </div>
                </div>

                <!-- الخطوة 2: طاقم التنشيط والمسار -->
                <div x-show="step === 2" class="space-y-4" style="display: none;">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">الميسر الرئيسي 1 *</label>
                            <select name="lead_facilitator_1_id" x-model="lead1" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition bg-white">
                                <option value="">— اختر الميسر الأول —</option>
                                @foreach($facilitators as $f)
                                    <option value="{{ $f->id }}">{{ $f->name }} @if(Auth::id() == $f->id) (أنت) @endif</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">الميسر الرئيسي 2 (اختياري)</label>
                            <select name="lead_facilitator_2_id" x-model="lead2"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-unicef-blue focus:ring-2 focus:ring-unicef-blue/20 transition bg-white">
                                <option value="">— اختر الميسر الثاني —</option>
                                @foreach($facilitators as $f)
                                    <option value="{{ $f->id }}" :disabled="lead1 == '{{ $f->id }}'">{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- المساعدون الستة -->
                    <div class="space-y-2">
                        <label class="block font-bold text-slate-700">الميسرون المساعدون (قوائم منسدلة 1 إلى 6 كالمعتمد في النموذج الرسمي):</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            @for($i = 0; $i < 6; $i++)
                                <div>
                                    <label class="block text-[10px] text-slate-500 mb-1">مساعد {{ $i + 1 }}:</label>
                                    <select name="assistant_facilitators[]"
                                        class="w-full p-2 rounded-lg border border-slate-200 text-xs text-slate-800 bg-white">
                                        <option value="">— غير محدد —</option>
                                        @foreach($facilitators as $f)
                                            <option value="{{ $f->name }}" :disabled="lead1 == '{{ $f->id }}' || lead2 == '{{ $f->id }}'">
                                                {{ $f->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- مسار الانتقال الميداني -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="font-bold text-slate-700">مسار الانتقال الميداني (حسب خانة الاستمارة الرسمية):</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">من بلدية:</label>
                                <input type="text" name="transit_from_municipality" x-model="transitFrom"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                    placeholder="بلدية الانطلاق">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">إلى بلدية:</label>
                                <input type="text" name="transit_to_municipality" x-model="transitTo"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                    placeholder="بلدية المقر">
                            </div>
                        </div>
                    </div>

                    <!-- أزرار التنقل -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 1" class="px-4 py-2 text-slate-600 font-bold hover:text-slate-900">&rarr; السابق</button>
                        <button type="button" @click="step = 3"
                            :disabled="!lead1"
                            class="px-6 py-2.5 rounded-xl bg-unicef-blue hover:bg-[#0E9BD3] disabled:opacity-50 text-white font-bold transition flex items-center gap-1.5 shadow-sm">
                            <span>التالي: المراجعة والتأكيد</span>
                            <span>&larr;</span>
                        </button>
                    </div>
                </div>

                <!-- الخطوة 3: المراجعة والتأكيد النهائي -->
                <div x-show="step === 3" class="space-y-4" style="display: none;">
                    <div class="p-4 rounded-xl bg-cyan-50/60 border border-unicef-blue/30 space-y-3">
                        <div class="font-black text-unicef-dark text-sm border-b border-unicef-blue/20 pb-2">
                            ملخص بيانات النشاط قبل الحفظ:
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-500">عنوان النشاط:</span>
                                <strong class="block text-slate-800" x-text="sessionTitle"></strong>
                            </div>
                            <div>
                                <span class="text-slate-500">التاريخ المقرر:</span>
                                <strong class="block text-slate-800 font-mono" x-text="sessionDate"></strong>
                            </div>
                            <div>
                                <span class="text-slate-500">المؤسسة المستقبلة:</span>
                                <strong class="block text-slate-800" x-text="institutions.find(i => i.id == selectedInstId)?.name || 'غير محدد'"></strong>
                            </div>
                            <div>
                                <span class="text-slate-500">الموقع:</span>
                                <strong class="block text-slate-800" x-text="selectedMunicipality + ' (' + selectedWilaya + ')'"></strong>
                            </div>
                            <div>
                                <span class="text-slate-500">الميسر المسؤول:</span>
                                <strong class="block text-slate-800">{{ Auth::user()->name }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500">المقر الدقيق:</span>
                                <strong class="block text-slate-800" x-text="venue || 'مقر المؤسسة'"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] flex items-center gap-2">
                        <span class="text-emerald-600 font-bold text-sm">✓</span>
                        <span>بمجرد الحفظ، سيتم تحويلك مباشرة إلى شاشة كشف الحضور لتسجيل اليافعين وتأكيد الحضور الميداني.</span>
                    </div>

                    <!-- أزرار الحفظ النهائي -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 2" class="px-4 py-2 text-slate-600 font-bold hover:text-slate-900">&rarr; السابق</button>
                        <button type="submit"
                            :disabled="!sessionTitle || !selectedInstId || !selectedWilaya || !selectedMunicipality || !lead1"
                            class="px-7 py-3 rounded-xl bg-unicef-blue hover:bg-[#0E9BD3] disabled:opacity-50 disabled:cursor-not-allowed text-white font-black text-xs uppercase tracking-wider transition shadow-md shadow-unicef-blue/30 flex items-center gap-2">
                            <span>[ ✓ ] حفظ وبدء تسجيل كشف الحضور</span>
                            <span>&larr;</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 2. النافذة المنبثقة لمعاينة النشاط (Session Preview Modal)      -->
    <!-- ============================================================== -->
    <div x-show="showPreviewModal && previewSession" class="fixed inset-0 z-50 bg-unicef-dark/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full max-h-[92vh] overflow-y-auto border border-slate-200 space-y-5 p-6"
             @click.away="showPreviewModal = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-unicef-light text-unicef-blue flex items-center justify-center font-bold">👁️</span>
                    <div>
                        <h3 class="font-black text-base text-unicef-dark" x-text="previewSession?.title"></h3>
                        <p class="text-[11px] text-slate-400 font-mono" x-text="'تاريخ الإجراء: ' + previewSession?.date_formatted"></p>
                    </div>
                </div>
                <button type="button" @click="showPreviewModal = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>
            </div>

            <!-- تفاصيل المعاينة -->
            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[10px]">المؤسسة المستقبلة:</span>
                        <strong class="text-slate-800" x-text="previewSession?.institution"></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">الموقع الجغرافي:</span>
                        <strong class="text-slate-800" x-text="previewSession?.municipality + ' (' + previewSession?.wilaya + ')'"></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">الميسر الرئيسي:</span>
                        <strong class="text-slate-800" x-text="previewSession?.lead1"></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">المسجلون بالحضور:</span>
                        <strong class="text-unicef-blue font-mono" x-text="previewSession?.attendees_count + ' يافع'"></strong>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[10px]">مسار الانتقال:</span>
                        <strong class="text-slate-700" x-text="'من بلدية: ' + previewSession?.transit_from + ' إلى: ' + previewSession?.transit_to"></strong>
                    </div>
                </div>

                <!-- أزرار الإجراءات داخل المعاينة -->
                <div class="space-y-2 pt-2">
                    <div class="grid grid-cols-2 gap-2">
                        <a :href="previewSession?.attendance_url"
                            class="py-2.5 px-3 rounded-xl bg-unicef-blue text-white text-center font-bold hover:bg-[#0E9BD3] transition flex items-center justify-center gap-1">
                            <span>📋 كشف الحضور</span>
                        </a>
                        <a :href="previewSession?.report_url"
                            class="py-2.5 px-3 rounded-xl bg-unicef-dark text-white text-center font-bold hover:bg-[#001D44] transition flex items-center justify-center gap-1">
                            <span>📝 تقرير النشاط</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                        <a :href="previewSession?.print_attendance_url" target="_blank"
                            class="py-2 px-3 rounded-xl border border-slate-200 text-slate-700 text-center font-bold hover:bg-slate-50 transition">
                            🖨️ طباعة كشف الحضور
                        </a>
                        <a :href="previewSession?.print_report_url" target="_blank"
                            class="py-2 px-3 rounded-xl border border-slate-200 text-slate-700 text-center font-bold hover:bg-slate-50 transition">
                            📑 طباعة التقرير الشامل
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
