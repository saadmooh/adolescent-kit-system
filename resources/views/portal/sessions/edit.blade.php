@extends('layouts.app')

@section('title', 'تعديل بيانات الجلسة - ' . $session->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" 
     x-data="{
        institutions: {{ $institutions->toJson() }},
        wilayasData: {{ json_encode($wilayasData, JSON_UNESCAPED_UNICODE) }},
        selectedInstId: '{{ $session->institution_id }}',
        selectedWilaya: '{{ $session->wilaya }}',
        selectedMunicipality: '{{ $session->municipality }}',
        lead1: '{{ $session->lead_facilitator_1_id }}',
        lead2: '{{ $session->lead_facilitator_2_id }}',
        
        onInstitutionChange() {
            const inst = this.institutions.find(i => i.id == this.selectedInstId);
            if (inst) {
                if (inst.wilaya && this.wilayasData[inst.wilaya]) {
                    this.selectedWilaya = inst.wilaya;
                }
                if (inst.municipality) {
                    this.selectedMunicipality = inst.municipality;
                }
            }
        },

        onWilayaChange() {
            const munics = this.wilayasData[this.selectedWilaya] || [];
            if (munics.length > 0) {
                this.selectedMunicipality = munics[0];
            } else {
                this.selectedMunicipality = '';
            }
        }
     }">

    <!-- ترويسة الصفحة الحادة بالأبيض والأسود -->
    <div class="bg-unicef-dark text-white p-6 border-2 border-unicef-dark flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-[4px_4px_0px_0px_rgba(0,47,108,1)]">
        <div>
            <div class="text-[10px] text-neutral-400 font-mono mb-1">بوابة التحكم / تعديل بيانات التدريب #{{ $session->id }}</div>
            <h1 class="text-xl font-black">تعديل الجلسة: {{ $session->title }}</h1>
            <p class="text-xs text-neutral-300 mt-1">تحديث الهيئة المستقبلة، طاقم الميسرين، المسار وحالة الجلسة.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('portal.sessions.attendance', $session) }}" class="px-3 py-1.5 bg-white text-unicef-dark hover:bg-neutral-200 font-bold text-xs border border-white transition">
                كشف الحضور &larr;
            </a>
            <a href="{{ route('portal.sessions.report', $session) }}" class="px-3 py-1.5 bg-unicef-darkBlue text-white hover:bg-unicef-darkBlue font-bold text-xs border border-white transition">
                تقرير النشاط &larr;
            </a>
            <a href="{{ route('portal.dashboard') }}" class="px-3 py-1.5 border border-white text-white hover:bg-white hover:text-unicef-dark font-bold text-xs transition">
                رجوع
            </a>
        </div>
    </div>

    <!-- نموذج تعديل الجلسة الحاد -->
    <form action="{{ route('portal.sessions.update', $session) }}" method="POST" class="bg-white p-6 sm:p-8 border-2 border-unicef-dark shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- 1. بيانات المؤسسة والموقع الجغرافي -->
        <div class="space-y-4">
            <div class="border-b-2 border-unicef-dark pb-2 flex items-center justify-between">
                <h2 class="font-black text-sm text-unicef-dark uppercase">
                    1. بيانات الجلسة والموقع الجغرافي
                </h2>
                <span class="text-[10px] text-neutral-500 font-mono">حقول مترابطة وذكية</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-unicef-dark mb-1">عنوان الدورة التدريبية *</label>
                    <input type="text" name="title" value="{{ old('title', $session->title) }}" required class="w-full p-2.5 text-xs font-bold">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">تاريخ إجراء النشاط *</label>
                    <input type="date" name="session_date" value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}" required class="w-full p-2.5 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">المؤسسة / الهيئة المستقبلة *</label>
                    <select name="institution_id" x-model="selectedInstId" @change="onInstitutionChange()" required class="w-full p-2.5 text-xs">
                        <option value="">— اختر المؤسسة المستقبلة —</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}">
                                {{ $inst->name }} ({{ $inst->municipality }} - {{ $inst->wilaya }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- علاقة الولاية والبلديات -->
                <div>
                    <label class="block font-bold text-unicef-dark mb-1">الولاية *</label>
                    <select name="wilaya" x-model="selectedWilaya" @change="onWilayaChange()" required class="w-full p-2.5 text-xs">
                        <template x-for="(munics, wName) in wilayasData" :key="wName">
                            <option :value="wName" x-text="wName" :selected="wName === selectedWilaya"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">البلدية * (مفلترة تلقائياً حسب الولاية)</label>
                    <select name="municipality" x-model="selectedMunicipality" required class="w-full p-2.5 text-xs">
                        <template x-for="m in (wilayasData[selectedWilaya] || [])" :key="m">
                            <option :value="m" x-text="m" :selected="m === selectedMunicipality"></option>
                        </template>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-unicef-dark mb-1">المكان الدقيق داخل المؤسسة</label>
                    <input type="text" name="venue" value="{{ old('venue', $session->venue) }}" placeholder="مثال: قاعة الأنشطة، المدرج، مكتبة..." class="w-full p-2.5 text-xs">
                </div>
            </div>
        </div>

        <!-- 2. طاقم الميسرين والمساعدين -->
        <div class="space-y-4 pt-4 border-t-2 border-unicef-dark">
            <div class="border-b-2 border-unicef-dark pb-2 flex items-center justify-between">
                <h2 class="font-black text-sm text-unicef-dark uppercase">
                    2. طاقم الميسرين والمساعدين
                </h2>
                <a href="{{ route('admin.dashboard') }}#facilitators-section" target="_blank" class="text-[11px] font-bold text-unicef-dark underline hover:bg-unicef-dark hover:text-white px-1">
                    [ + ] إدارة قائمة الميسرين
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-unicef-dark mb-1">الميسر الرئيسي 1 *</label>
                    <select name="lead_facilitator_1_id" x-model="lead1" required class="w-full p-2.5 text-xs">
                        <option value="">— اختر الميسر الأول —</option>
                        @foreach($facilitators as $f)
                            <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">الميسر الرئيسي 2 (معطل تلقائياً إذا اختير في الخانة 1)</label>
                    <select name="lead_facilitator_2_id" x-model="lead2" class="w-full p-2.5 text-xs">
                        <option value="">— اختر الميسر الثاني (اختياري) —</option>
                        @foreach($facilitators as $f)
                            <option value="{{ $f->id }}" :disabled="lead1 == '{{ $f->id }}'">
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- المساعدون الستة المذكورين في الاستمارة الرسمية (قوائم منسدلة من قائمة الميسرين) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-unicef-dark">الميسرين المساعدين (قوائم منسدلة من 1 إلى 6 كالمعتمد في النموذج الرسمي):</label>
                    <span class="text-[10px] text-neutral-500 font-mono">اختيار من قائمة الميسرين المعتمدين</span>
                </div>
                @php $assts = $session->assistant_facilitators ?? []; @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @for($i = 0; $i < 6; $i++)
                        @php $currentVal = $assts[$i] ?? ''; @endphp
                        <div>
                            <label class="block text-[11px] font-bold text-neutral-700 mb-0.5">المساعد {{ $i + 1 }}:</label>
                            <select name="assistant_facilitators[]" class="w-full p-2 text-xs">
                                <option value="">— اختر مساعد {{ $i + 1 }} (اختياري) —</option>
                                @foreach($facilitators as $f)
                                    <option value="{{ $f->name }}" {{ $currentVal === $f->name ? 'selected' : '' }} :disabled="lead1 == '{{ $f->id }}' || lead2 == '{{ $f->id }}'">
                                        {{ $f->name }} @if($f->role === 'assistant') [مساعد] @elseif($f->role === 'admin') [مدير] @else [ميسر] @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- 3. مسار التنقل الميداني (الانتقال) -->
        <div class="space-y-4 pt-4 border-t-2 border-unicef-dark">
            <div class="border-b-2 border-unicef-dark pb-2">
                <h2 class="font-black text-sm text-unicef-dark uppercase">
                    3. مسار الانتقال الميداني وحالة الجلسة
                </h2>
                <p class="text-[10px] text-neutral-500 mt-0.5">وفق خانة الاستمارة الرسمية: «الانتقال: من بلدية ........ الى بلدية ........»</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-unicef-dark mb-1">من بلدية:</label>
                    <input type="text" name="transit_from_municipality" value="{{ old('transit_from_municipality', $session->transit_from_municipality) }}" placeholder="بلدية الانطلاق" class="w-full p-2.5 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">إلى بلدية:</label>
                    <input type="text" name="transit_to_municipality" value="{{ old('transit_to_municipality', $session->transit_to_municipality) }}" placeholder="بلدية الوصول / المقر" class="w-full p-2.5 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">حالة الجلسة والاعتماد *</label>
                    <select name="status" required class="w-full p-2.5 text-xs font-bold">
                        <option value="draft" {{ $session->status === 'draft' ? 'selected' : '' }}>مسودة أولية (Draft)</option>
                        <option value="ongoing" {{ $session->status === 'ongoing' ? 'selected' : '' }}>جارية ميدانياً (Ongoing)</option>
                        <option value="completed" {{ $session->status === 'completed' ? 'selected' : '' }}>مكتملة وبانتظار الاعتماد (Completed)</option>
                        <option value="approved" {{ $session->status === 'approved' ? 'selected' : '' }}>معتمدة رسمياً (Approved)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t-2 border-unicef-dark">
            <span class="text-[11px] text-neutral-500 font-mono">آخر تعديل: {{ $session->updated_at->format('Y-m-d H:i') }}</span>
            <div class="flex items-center gap-3">
                <a href="{{ route('portal.dashboard') }}" class="px-5 py-2.5 border-2 border-unicef-dark font-bold text-unicef-dark hover:bg-neutral-100">إلغاء</a>
                <button type="submit" class="px-8 py-2.5 bg-unicef-dark text-white border-2 border-unicef-dark hover:bg-unicef-darkBlue font-bold uppercase transition">
                    [ ✓ ] حفظ وتحديث بيانات الجلسة
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
