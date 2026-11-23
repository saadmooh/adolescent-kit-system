@extends('layouts.app')

@section('title', 'إنشاء جلسة تدريب جديدة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-xl font-black text-slate-900">إنشاء جلسة تدريب جديدة</h1>
            <p class="text-xs text-slate-500 mt-1">تحديد الهيئة المستقبلة، طاقم الميسرين والمساعدين، ومسار التنقل الميداني.</p>
        </div>
        <a href="{{ route('sessions.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">&rarr; رجوع للقائمة</a>
    </div>

    <form action="{{ route('sessions.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6 text-xs">
        @csrf

        <!-- البيانات العامة للجلسة -->
        <div class="space-y-4">
            <h2 class="font-black text-sm text-slate-800 border-b pb-2 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-600"></span>
                <span>بيانات التدريب والموقع الجغرافي</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">عنوان التدريب / النشاط *</label>
                    <input type="text" name="title" required value="تدريب حقيبة الابتكار والتعبير لليافعين Kit Adolescent" class="w-full rounded-xl border-slate-300 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ النشاط *</label>
                    <input type="date" name="session_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">المؤسسة المستقبلة *</label>
                    <select name="institution_id" required class="w-full rounded-xl border-slate-300 text-xs">
                        <option value="">— اختر المؤسسة —</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }} ({{ $inst->municipality }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">المكان الدقيق داخل المؤسسة</label>
                    <input type="text" name="venue" placeholder="مثال: قاعة الأنشطة الثقافية، المدرج، مكتبة..." class="w-full rounded-xl border-slate-300 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">البلدية *</label>
                    <input type="text" name="municipality" required placeholder="مثال: باب الوادي" class="w-full rounded-xl border-slate-300 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الولاية *</label>
                    <input type="text" name="wilaya" required placeholder="مثال: الجزائر" class="w-full rounded-xl border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <!-- طاقم الميسرين والمساعدين -->
        <div class="space-y-4 pt-4 border-t">
            <h2 class="font-black text-sm text-slate-800 border-b pb-2 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                <span>طاقم الميسرين والمساعدين (المحددين في الوثيقة)</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الميسر الرئيسي 1 *</label>
                    <select name="lead_facilitator_1_id" class="w-full rounded-xl border-slate-300 text-xs">
                        <option value="">— اختر الميسر —</option>
                        @foreach($facilitators as $f)
                            <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الميسر الرئيسي 2</label>
                    <select name="lead_facilitator_2_id" class="w-full rounded-xl border-slate-300 text-xs">
                        <option value="">— اختر الميسر الثاني (اختياري) —</option>
                        @foreach($facilitators as $f)
                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- المساعدون الستة المذكورين في الاستمارة الرسمية (قوائم منسدلة) -->
            <div>
                <label class="block font-bold text-slate-700 mb-2">الميسرين المساعدين (قوائم منسدلة من 1 إلى 6 كالمعتمد في النموذج الرسمي):</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @for($i = 0; $i < 6; $i++)
                        <div>
                            <select name="assistant_facilitators[]" class="w-full rounded-none border border-black text-xs p-2">
                                <option value="">— اختر مساعد {{ $i + 1 }} (اختياري) —</option>
                                @foreach($facilitators as $f)
                                    <option value="{{ $f->name }}">{{ $f->name }} ({{ $f->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- مسار الانتقال -->
        <div class="space-y-4 pt-4 border-t">
            <h2 class="font-black text-sm text-slate-800 border-b pb-2 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span>الانتقال والتنقل الميداني</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">من بلدية:</label>
                    <input type="text" name="transit_from_municipality" placeholder="بلدية الانطلاق" class="w-full rounded-xl border-slate-300 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">إلى بلدية:</label>
                    <input type="text" name="transit_to_municipality" placeholder="بلدية الوصول / المقر" class="w-full rounded-xl border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t">
            <a href="{{ route('sessions.index') }}" class="px-5 py-2.5 border rounded-xl font-bold text-slate-600 hover:bg-slate-50">إلغاء</a>
            <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl shadow-md">
                إنشاء الجلسة والانتقال للحضور
            </button>
        </div>
    </form>

</div>
@endsection
