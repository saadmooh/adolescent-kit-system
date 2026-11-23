@extends('layouts.app')

@section('title', 'لوحة التحكم والقيادة')

@section('content')
<div class="space-y-6">

    <!-- الترويسة الرئيسية -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">مرحباً بك في منظومة متابعة حقيبة الابتكار Kit Adolescent</h1>
            <p class="text-sm text-slate-500 mt-1">تتبع الحضور الميداني، إحصائيات اليافعين، تقارير النشاط، والأرشفة الإعلامية المعتمدة.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sessions.create') }}" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-sm shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>إنشاء جلسة جديدة</span>
            </a>
        </div>
    </div>

    <!-- بطاقات المؤشرات الأساسية (KPIs) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- إجمالي الجلسات المنفذة -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-black text-xl">
                📋
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500">الجلسات والتدريبات</div>
                <div class="text-2xl font-black text-slate-900">{{ $totalSessions }}</div>
            </div>
        </div>

        <!-- إجمالي اليافعين المستفيدين -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-xl">
                👥
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500">إجمالي اليافعين المسجلين</div>
                <div class="text-2xl font-black text-slate-900">{{ $totalBeneficiaries }}</div>
            </div>
        </div>

        <!-- التوزيع بين الجنسين -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    ⚧
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500">الذكور / الإناث</div>
                    <div class="text-sm font-black text-slate-800">
                        <span class="text-blue-600">{{ $maleCount }} ذكر</span>
                        <span class="text-slate-300 mx-1">|</span>
                        <span class="text-pink-600">{{ $femaleCount }} أنثى</span>
                    </div>
                </div>
            </div>
            <div class="text-xs text-slate-400 font-mono">
                {{ $totalBeneficiaries > 0 ? round(($femaleCount / $totalBeneficiaries) * 100) : 0 }}% إناث
            </div>
        </div>

        <!-- الفئات الخاصة والإعاقة -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-xl">
                ♿
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500">ذوو الإعاقة والأمراض المزمنة</div>
                <div class="text-sm font-black text-slate-800">
                    <span class="text-amber-700">{{ $disabledCount }} إعاقة</span>
                    <span class="text-slate-300 mx-1">|</span>
                    <span class="text-purple-700">{{ $chronicCount }} مزمن</span>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الجلسات الأخيرة -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                <h2 class="font-bold text-slate-800 text-base">أحدث الجلسات التدريبية المجدولة والمنفذة</h2>
            </div>
            <a href="{{ route('sessions.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700">عرض كافة الجلسات &larr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 text-xs">
                    <tr>
                        <th class="py-3.5 px-4">عنوان التدريب</th>
                        <th class="py-3.5 px-4">التاريخ</th>
                        <th class="py-3.5 px-4">المؤسسة والبلدية</th>
                        <th class="py-3.5 px-4">الميسر الرئيسي</th>
                        <th class="py-3.5 px-4">المسجلون</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">إجراءات سريعة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($recentSessions as $session)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 font-bold text-slate-900">
                                <a href="{{ route('sessions.show', $session) }}" class="hover:text-sky-600 transition">
                                    {{ $session->title }}
                                </a>
                            </td>
                            <td class="py-4 px-4 text-slate-500 whitespace-nowrap">
                                {{ $session->session_date->format('Y/m/d') }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-slate-800">{{ $session->institution->name ?? 'غير محددة' }}</div>
                                <div class="text-xs text-slate-400">بلدية {{ $session->municipality }}</div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                {{ $session->leadFacilitator1->name ?? '—' }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700">
                                    {{ $session->attendances->count() }} يافع
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @if($session->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">مكتمل</span>
                                @elseif($session->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700">معتمد</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700">مسودة</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('sessions.attendance.index', $session) }}" title="إدارة الحضور" class="p-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 transition">
                                        📋 حضور
                                    </a>
                                    <a href="{{ route('sessions.reports.edit', $session) }}" title="تقرير النشاط" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition">
                                        📝 تقرير
                                    </a>
                                    <a href="{{ route('sessions.print.attendance', $session) }}" target="_blank" title="طباعة كشف الحضور" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                        🖨️ كشف
                                    </a>
                                    <a href="{{ route('sessions.print.report', $session) }}" target="_blank" title="طباعة التقرير الشامل" class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                        📑 تقرير
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                لا توجد جلسات تدريبية مسجلة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
