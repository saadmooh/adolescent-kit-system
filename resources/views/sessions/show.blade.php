@extends('layouts.app')

@section('title', $session->title)

@section('content')
<div class="space-y-6">

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-sky-600 mb-1">
                <span>الجلسات</span>
                <span>/</span>
                <span>تفاصيل الجلسة التدريبية</span>
            </div>
            <h1 class="text-xl font-black text-slate-900">{{ $session->title }}</h1>
            <div class="text-xs text-slate-500 mt-1 flex flex-wrap gap-4">
                <span>🏫 المؤسسة: <strong>{{ $session->institution->name }}</strong></span>
                <span>📍 البلدية: <strong>{{ $session->municipality }} ({{ $session->wilaya }})</strong></span>
                <span>📅 التاريخ: <strong>{{ $session->session_date->format('Y/m/d') }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('sessions.attendance.index', $session) }}" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                <span>📋 كشف الحضور ({{ $session->attendances->count() }})</span>
            </a>
            <a href="{{ route('sessions.reports.edit', $session) }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                <span>📝 تقرير النشاط</span>
            </a>
            <a href="{{ route('sessions.print.attendance', $session) }}" target="_blank" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition" title="طباعة كشف الحضور">
                🖨️ كشف
            </a>
            <a href="{{ route('sessions.print.report', $session) }}" target="_blank" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition" title="طباعة تقرير النشاط">
                📑 تقرير
            </a>
        </div>
    </div>

    <!-- بطاقات تفصيلية سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- طاقم العمل والانتقال -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3 text-xs">
            <h3 class="font-black text-sm text-slate-800 border-b pb-2">طاقم التدريب والمسار الميداني</h3>
            
            <div class="space-y-1.5 text-slate-600">
                <div>الميسر الرئيسي 1: <strong>{{ $session->leadFacilitator1->name ?? 'غير محدد' }}</strong></div>
                <div>الميسر الرئيسي 2: <strong>{{ $session->leadFacilitator2->name ?? 'غير محدد' }}</strong></div>
                <div>
                    المساعدون: 
                    @php $assts = $session->assistant_facilitators ?? []; @endphp
                    <strong>{{ !empty($assts) ? implode('، ', array_filter($assts)) : 'لا يوجد مساعدون مسجلون' }}</strong>
                </div>
                <div class="pt-2 border-t text-slate-500">
                    خط الانتقال: من بلدية <strong>{{ $session->transit_from_municipality ?: $session->municipality }}</strong> إلى بلدية <strong>{{ $session->transit_to_municipality ?: $session->municipality }}</strong>
                </div>
            </div>
        </div>

        <!-- حالة التقرير والاعتماد -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3 text-xs">
            <h3 class="font-black text-sm text-slate-800 border-b pb-2">حالة التقرير والمصادقة</h3>

            @if($session->activityReport)
                <div class="space-y-1.5 text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>الأنشطة المحددة:</span>
                        <strong class="font-mono text-sky-700">{{ count($session->activityReport->selected_activities ?? []) }} نشاط</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>المهارات المحققة:</span>
                        <strong class="font-mono text-emerald-700">{{ count($session->activityReport->selected_competencies ?? []) }} مهارة</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>التوثيق الإعلامي:</span>
                        <strong class="font-mono text-amber-700">{{ $session->activityReport->mediaAttachments->count() }} مرفقات</strong>
                    </div>
                    <div class="pt-2 border-t flex items-center justify-between">
                        <span>المقرر:</span>
                        <strong>{{ $session->activityReport->rapporteur->name ?? $session->leadFacilitator1->name ?? '—' }}</strong>
                    </div>
                </div>
            @else
                <div class="py-6 text-center text-slate-400">
                    لم يتم تعبئة تقرير النشاط بعد.
                    <div class="mt-2">
                        <a href="{{ route('sessions.reports.edit', $session) }}" class="text-sky-600 font-bold hover:underline">بدء تعبئة التقرير الآن &larr;</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
