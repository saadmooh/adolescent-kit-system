@extends('layouts.app')

@section('title', 'قائمة حضور اليافعين - ' . $session->title)

@section('content')
<div class="space-y-6" x-data="{ showAddModal: false }">

    <!-- شريط الإجراءات والترويسة -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-sky-600 mb-1">
                <span>الجلسة التدريبية</span>
                <span>/</span>
                <span>قائمة الحضور والتسجيل</span>
            </div>
            <h1 class="text-xl font-black text-slate-900">{{ $session->title }}</h1>
            <div class="text-xs text-slate-500 mt-1 flex flex-wrap gap-4">
                <span>🏫 المؤسسة: <strong>{{ $session->institution->name }}</strong></span>
                <span>📍 البلدية: <strong>{{ $session->municipality }}</strong></span>
                <span>📅 التاريخ: <strong>{{ $session->session_date->format('Y/m/d') }}</strong></span>
                <span>👤 الميسر: <strong>{{ $session->leadFacilitator1->name ?? 'غير محدد' }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button @click="showAddModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>تسجيل يافع جديد</span>
            </button>
            <a href="{{ route('sessions.print.attendance', $session) }}" target="_blank" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>طباعة الكشف الرسمي</span>
            </a>
            <a href="{{ route('sessions.reports.edit', $session) }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                <span>الانتقال لتقرير النشاط &larr;</span>
            </a>
        </div>
    </div>

    <!-- شريط الحسابات الإحصائية الفورية -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-xs text-slate-500 font-semibold">إجمالي الحضور</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $stats['total_attendees'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-xs text-slate-500 font-semibold">الذكور</div>
            <div class="text-xl font-black text-blue-600 mt-1">{{ $stats['male_count'] }} <span class="text-xs text-slate-400">({{ $stats['male_percentage'] }}%)</span></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-xs text-slate-500 font-semibold">الإناث</div>
            <div class="text-xl font-black text-pink-600 mt-1">{{ $stats['female_count'] }} <span class="text-xs text-slate-400">({{ $stats['female_percentage'] }}%)</span></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-xs text-slate-500 font-semibold">المتمدرسون</div>
            <div class="text-xl font-black text-emerald-600 mt-1">{{ $stats['schooled_count'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-xs text-slate-500 font-semibold">أمراض مزمنة</div>
            <div class="text-xl font-black text-amber-600 mt-1">{{ $stats['chronic_diseases_count'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-xs text-slate-500 font-semibold">ذوو إعاقة</div>
            <div class="text-xl font-black text-purple-600 mt-1">{{ $stats['disabled_count'] }}</div>
        </div>
    </div>

    <!-- جدول كشف الحضور المطابق للنموذج الرسمي -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="font-bold text-slate-800 text-sm">
                قائمة اليافعين المسجلين في هذا النشاط (الحد الأقصى المعتمد 70 مقعداً)
            </div>
            <div class="text-xs text-slate-500">
                يتم احتساب الإحصائيات في تقرير النشاط تلقائياً بناءً على الحاضرين المؤكدين فقط.
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 text-center w-12">الرقم</th>
                        <th class="py-3 px-3">الاسم واللقب</th>
                        <th class="py-3 px-3">تاريخ الميلاد (العمر)</th>
                        <th class="py-3 px-3">البلدية</th>
                        <th class="py-3 px-3">المستوى الدراسي</th>
                        <th class="py-3 px-3 text-center">الجنس</th>
                        <th class="py-3 px-3">المرض المزمن / الإعاقة</th>
                        <th class="py-3 px-3 text-center">تأكيد الحضور</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                    @forelse($attendances as $att)
                        @php $b = $att->beneficiary; @endphp
                        <tr class="hover:bg-slate-50 transition {{ !$att->attended ? 'opacity-40 bg-slate-50/50' : '' }}">
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-500">
                                {{ sprintf('%02d', $att->serial_number) }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ $b->full_name }}
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                {{ $b->birth_date->format('Y/m/d') }}
                                <span class="text-slate-400 font-mono">({{ $b->getAgeAtDate($session->session_date) }} سنة)</span>
                            </td>
                            <td class="py-3 px-3">{{ $b->municipality }}</td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold">
                                    {{ $b->education_level }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($b->gender === 'ذكر')
                                    <span class="px-2 py-0.5 rounded-full font-bold bg-blue-50 text-blue-700">ذكر</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full font-bold bg-pink-50 text-pink-700">أنثى</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($b->has_chronic_disease)
                                    <span class="inline-block px-1.5 py-0.5 bg-amber-50 text-amber-800 rounded font-bold text-[11px] mb-0.5">
                                        مزمن: {{ $b->chronic_disease_details ?: 'نعم' }}
                                    </span>
                                @endif
                                @if($b->has_disability)
                                    <span class="inline-block px-1.5 py-0.5 bg-purple-50 text-purple-800 rounded font-bold text-[11px]">
                                        إعاقة: {{ $b->disability_type }}
                                    </span>
                                @endif
                                @if(!$b->has_chronic_disease && !$b->has_disability)
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                <form action="{{ route('sessions.attendance.toggle', [$session, $att]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ $att->attended ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                                        {{ $att->attended ? '✓ حاضر' : '✗ غائب' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                لا يوجد يافعون مسجلون في هذه الجلسة حتى الآن. انقر على زر "تسجيل يافع جديد" أعلاه.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- نافذة منبثقة لتسجيل يافع جديد (Modal) -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4" @click.away="showAddModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-black text-lg text-slate-900">تسجيل يافع جديد في الجلسة</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form action="{{ route('sessions.attendance.storeBeneficiary', $session) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الاسم واللقب *</label>
                        <input type="text" name="full_name" required class="w-full rounded-xl border-slate-300 focus:ring-sky-500 focus:border-sky-500 text-xs" placeholder="مثال: محمد العمري">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ الميلاد *</label>
                        <input type="date" name="birth_date" required class="w-full rounded-xl border-slate-300 focus:ring-sky-500 focus:border-sky-500 text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الجنس *</label>
                        <select name="gender" required class="w-full rounded-xl border-slate-300 focus:ring-sky-500 focus:border-sky-500 text-xs">
                            <option value="ذكر">ذكر</option>
                            <option value="أنثى">أنثى</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">البلدية *</label>
                        <input type="text" name="municipality" value="{{ $session->municipality }}" required class="w-full rounded-xl border-slate-300 focus:ring-sky-500 focus:border-sky-500 text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">المستوى الدراسي *</label>
                        <select name="education_level" class="w-full rounded-xl border-slate-300 focus:ring-sky-500 focus:border-sky-500 text-xs">
                            <option value="ابتدائي">ابتدائي</option>
                            <option value="متوسط" selected>متوسط</option>
                            <option value="ثانوي">ثانوي</option>
                            <option value="تكوين مهني">تكوين مهني</option>
                            <option value="غير متمدرس">غير متمدرس</option>
                        </select>
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_schooled" value="1" checked class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="font-bold text-slate-700">متمدرس حالياً</span>
                        </label>
                    </div>
                </div>

                <!-- المرض المزمن -->
                <div class="border-t pt-3" x-data="{ hasChronic: false }">
                    <label class="flex items-center gap-2 cursor-pointer mb-2">
                        <input type="checkbox" name="has_chronic_disease" value="1" x-model="hasChronic" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <span class="font-bold text-slate-800">يعاني من مرض مزمن؟</span>
                    </label>
                    <div x-show="hasChronic">
                        <input type="text" name="chronic_disease_details" placeholder="نوع المرض المزمن (مثل: ربو، سكري، حساسية...)" class="w-full rounded-xl border-slate-300 text-xs">
                    </div>
                </div>

                <!-- الإعاقة -->
                <div class="border-t pt-3" x-data="{ hasDisability: false }">
                    <label class="flex items-center gap-2 cursor-pointer mb-2">
                        <input type="checkbox" name="has_disability" value="1" x-model="hasDisability" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                        <span class="font-bold text-slate-800">من فئة ذوي الإعاقة؟</span>
                    </label>
                    <div x-show="hasDisability" class="grid grid-cols-2 gap-3">
                        <div>
                            <select name="disability_type" class="w-full rounded-xl border-slate-300 text-xs">
                                <option value="حركية">حركية</option>
                                <option value="بصرية">بصرية</option>
                                <option value="الصم البكم">الصم البكم</option>
                                <option value="شيء آخر">شيء آخر</option>
                            </select>
                        </div>
                        <div>
                            <input type="text" name="disability_details" placeholder="تفاصيل إضافية حول الإعاقة" class="w-full rounded-xl border-slate-300 text-xs">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-bold">إلغاء</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">تسجيل وتأكيد الحضور</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
