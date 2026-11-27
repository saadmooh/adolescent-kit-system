@extends('layouts.app')

@section('title', 'إنشاء جلسة تدريب جديدة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" 
     x-data="{
        institutions: {{ $institutions->toJson() }},
        wilayasData: {{ json_encode($wilayasData, JSON_UNESCAPED_UNICODE) }},
        selectedInstId: '',
        selectedWilaya: 'الجزائر',
        selectedMunicipality: 'باب الوادي',
        lead1: '{{ Auth::id() ?? '' }}',
        lead2: '',
        
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
    <div class="bg-unicef-dark text-white p-6 border-2 border-unicef-dark flex items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,47,108,1)]">
        <div>
            <div class="text-[10px] text-neutral-400 font-mono mb-1">بوابة الإدخال الميداني / خطوة 1</div>
            <h1 class="text-xl font-black">إنشاء جلسة نشاط جديدة (حقيبة الابتكار Kit Adolescent)</h1>
            <p class="text-xs text-neutral-300 mt-1">تحديد الهيئة المستقبلة، طاقم الميسرين، ومسار الانتقال الميداني.</p>
        </div>
        <a href="{{ route('portal.dashboard') }}" class="px-3 py-1.5 border border-white text-white hover:bg-white hover:text-unicef-dark font-bold text-xs transition">
            &larr; رجوع للجلسات
        </a>
    </div>

    <!-- نموذج إنشاء الجلسة الحاد -->
    <form action="{{ route('portal.sessions.store') }}" method="POST" class="bg-white p-6 sm:p-8 border-2 border-unicef-dark shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-6 text-xs">
        @csrf

        <!-- 1. بيانات المؤسسة والموقع الجغرافي -->
        <div class="space-y-4">
            <div class="border-b-2 border-unicef-dark pb-2 flex items-center justify-between">
                <h2 class="font-black text-sm text-unicef-dark uppercase">
                    1. تحديد المؤسسة والموقع الجغرافي
                </h2>
                <span class="text-[10px] text-neutral-500 font-mono">اختيار المؤسسة يملأ الموقع تلقائياً</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-unicef-dark mb-1">عنوان التدريب / النشاط *</label>
                    <input type="text" name="title" required value="تدريب حقيبة الابتكار والتعبير لليافعين Kit Adolescent" class="w-full p-2.5 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">تاريخ النشاط *</label>
                    <input type="date" name="session_date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">المؤسسة المستقبلة *</label>
                    <select name="institution_id" x-model="selectedInstId" @change="onInstitutionChange()" required class="w-full p-2.5 text-xs">
                        <option value="">— اختر المؤسسة المستقبلة —</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }} ({{ $inst->municipality }})</option>
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
                    <input type="text" name="venue" placeholder="مثال: قاعة الأنشطة، المدرج، مكتبة..." class="w-full p-2.5 text-xs">
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
                    [ + ] إدخال ميسرين جدد في القائمة المعتمدة
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
                                {{ $f->name }} @if(Auth::id() == $f->id) (أنت) @endif
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @for($i = 0; $i < 6; $i++)
                        <div>
                            <label class="block text-[11px] font-bold text-neutral-700 mb-0.5">المساعد {{ $i + 1 }}:</label>
                            <select name="assistant_facilitators[]" class="w-full p-2 text-xs">
                                <option value="">— اختر مساعد {{ $i + 1 }} (اختياري) —</option>
                                @foreach($facilitators as $f)
                                    <option value="{{ $f->name }}" :disabled="lead1 == '{{ $f->id }}' || lead2 == '{{ $f->id }}'">
                                        {{ $f->name }} @if($f->role === 'assistant') [مساعد] @elseif($f->role === 'admin') [مدير] @else [ميسر] @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- 3. مسار التنقل الميداني (الانتقال) إدخال يدوي صريح دون مطابقة تلقائية -->
        <div class="space-y-4 pt-4 border-t-2 border-unicef-dark">
            <div class="border-b-2 border-unicef-dark pb-2">
                <h2 class="font-black text-sm text-unicef-dark uppercase">
                    3. مسار الانتقال الميداني
                </h2>
                <p class="text-[10px] text-neutral-500 mt-0.5">وفق خانة الاستمارة الرسمية: «الانتقال: من بلدية ........ الى بلدية ........»</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-unicef-dark mb-1">من بلدية:</label>
                    <input type="text" name="transit_from_municipality" placeholder="بلدية الانطلاق" class="w-full p-2.5 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-unicef-dark mb-1">إلى بلدية:</label>
                    <input type="text" name="transit_to_municipality" placeholder="بلدية الوصول / المقر" class="w-full p-2.5 text-xs">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t-2 border-unicef-dark">
            <a href="{{ route('portal.dashboard') }}" class="px-5 py-2.5 border-2 border-unicef-dark font-bold text-unicef-dark hover:bg-neutral-100">إلغاء</a>
            <button type="submit" class="px-8 py-2.5 bg-unicef-dark text-white border-2 border-unicef-dark hover:bg-unicef-darkBlue font-bold uppercase transition">
                حفظ وبدء تسجيل كشف الحضور &larr;
            </button>
        </div>
    </form>

</div>
@endsection
