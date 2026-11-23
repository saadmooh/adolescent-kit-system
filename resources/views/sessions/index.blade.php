@extends('layouts.app')

@section('title', 'الجلسات والتدريبات')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-xl font-black text-slate-900">سجل الجلسات والتدريبات الميدانية</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة كافة دورات حقيبة الابتكار والتعبير لليافعين وتتبع كشوف الحضور والتقارير.</p>
        </div>
        <a href="{{ route('sessions.create') }}" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>إنشاء جلسة تدريب جديدة</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">عنوان الجلسة</th>
                        <th class="py-3.5 px-4">التاريخ</th>
                        <th class="py-3.5 px-4">المؤسسة المستضيفة</th>
                        <th class="py-3.5 px-4">البلدية والولاية</th>
                        <th class="py-3.5 px-4">الميسر الرئيسي</th>
                        <th class="py-3.5 px-4">الحضور المسجل</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4 font-bold text-slate-900">
                                <a href="{{ route('sessions.show', $session) }}" class="hover:text-sky-600">
                                    {{ $session->title }}
                                </a>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">{{ $session->session_date->format('Y/m/d') }}</td>
                            <td class="py-4 px-4">{{ $session->institution->name }}</td>
                            <td class="py-4 px-4">{{ $session->municipality }} ({{ $session->wilaya }})</td>
                            <td class="py-4 px-4">{{ $session->leadFacilitator1->name ?? '—' }}</td>
                            <td class="py-4 px-4 font-mono font-bold text-sky-700">
                                {{ $session->attendances->count() }} يافع
                            </td>
                            <td class="py-4 px-4">
                                @if($session->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-50 text-emerald-700">مكتمل</span>
                                @elseif($session->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full font-bold bg-purple-50 text-purple-700">معتمد وموقع</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full font-bold bg-amber-50 text-amber-700">مسودة</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('sessions.attendance.index', $session) }}" class="px-2.5 py-1 rounded bg-sky-50 text-sky-700 hover:bg-sky-100 font-bold">
                                        كشف الحضور
                                    </a>
                                    <a href="{{ route('sessions.reports.edit', $session) }}" class="px-2.5 py-1 rounded bg-amber-50 text-amber-700 hover:bg-amber-100 font-bold">
                                        تقرير النشاط
                                    </a>
                                    <a href="{{ route('sessions.print.attendance', $session) }}" target="_blank" class="p-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-600" title="طباعة كشف الحضور">
                                        🖨️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                لا توجد جلسات مسجلة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
