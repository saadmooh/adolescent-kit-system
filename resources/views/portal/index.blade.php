@extends('layouts.app')

@section('title', 'بوابة الإدخال الميداني للميسرين')

@section('content')
<div class="space-y-6">

    <!-- الترويسة الحادة للميسر الميداني -->
    <div class="bg-black text-white p-6 sm:p-8 border-2 border-black flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <div class="space-y-1">
            <div class="inline-block border border-white px-2 py-0.5 text-[10px] font-mono font-bold uppercase mb-2">
                بوابة الميسر الميداني (Field Entry Portal)
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">أهلاً بك، {{ Auth::user()->name }}</h1>
            <p class="text-xs text-neutral-300 max-w-xl">
                الإدخال الميداني المباشر: تسجيل كشوف حضور اليافعين لحظياً، ملء تقارير الأنشطة، وتوثيق الصور والفيديو.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('portal.sessions.create') }}" class="px-5 py-3 bg-white text-black hover:bg-neutral-200 border-2 border-white font-black text-xs uppercase tracking-wider transition">
                [ + ] إنشاء جلسة تدريب جديدة
            </a>
        </div>
    </div>

    <!-- شبكة الجلسات الميدانية للميسر -->
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b-2 border-black pb-2">
            <h2 class="text-base font-black text-black uppercase">
                الجلسات التدريبية المخصصة لك
            </h2>
            <span class="border border-black px-2 py-0.5 font-mono text-xs font-bold bg-neutral-100">
                إجمالي: {{ $mySessions->total() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($mySessions as $session)
                <div class="bg-white border-2 border-black p-5 flex flex-col justify-between space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs border-b border-black pb-2">
                            <span class="font-mono font-bold">{{ $session->session_date->format('Y/m/d') }}</span>
                            @if($session->status === 'approved')
                                <span class="border border-black px-1.5 py-0.5 bg-black text-white text-[10px] font-bold">معتمد</span>
                            @elseif($session->status === 'completed')
                                <span class="border border-black px-1.5 py-0.5 bg-neutral-200 text-black text-[10px] font-bold">مكتمل</span>
                            @else
                                <span class="border border-neutral-400 px-1.5 py-0.5 text-neutral-600 text-[10px] font-bold">مسودة</span>
                            @endif
                        </div>

                        <div>
                            <h3 class="font-black text-sm text-black leading-tight">{{ $session->title }}</h3>
                            <div class="text-xs text-neutral-600 mt-1">
                                <div>المؤسسة: <strong>{{ $session->institution->name }}</strong></div>
                                <div>البلدية: <strong>{{ $session->municipality }} ({{ $session->wilaya }})</strong></div>
                            </div>
                        </div>

                        <div class="p-2 border border-black bg-neutral-50 flex items-center justify-between text-xs font-bold">
                            <span>المسجلون بالحضور:</span>
                            <span class="font-mono text-black">{{ $session->attendances->count() }} / 70 مقعد</span>
                        </div>
                    </div>

                    <!-- الأزرار بالأبيض والأسود الحاد -->
                    <div class="space-y-2 pt-2 border-t-2 border-black">
                        <div class="grid grid-cols-3 gap-1.5">
                            <a href="{{ route('portal.sessions.attendance', $session) }}" class="p-2 text-center bg-black text-white hover:bg-neutral-800 font-bold text-xs border border-black transition">
                                كشف الحضور
                            </a>
                            <a href="{{ route('portal.sessions.report', $session) }}" class="p-2 text-center bg-white text-black hover:bg-neutral-100 font-bold text-xs border border-black transition">
                                تقرير النشاط
                            </a>
                            <a href="{{ route('portal.sessions.edit', $session) }}" class="p-2 text-center bg-neutral-100 text-black hover:bg-black hover:text-white font-bold text-xs border border-black transition">
                                ✏️ تعديل
                            </a>
                        </div>

                        <div class="flex items-center justify-between text-[11px] pt-1">
                            <a href="{{ route('sessions.print.attendance', $session) }}" target="_blank" class="underline font-bold text-black">🖨️ طباعة كشف الحضور</a>
                            <a href="{{ route('sessions.print.report', $session) }}" target="_blank" class="underline font-bold text-black">📑 طباعة التقرير</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border-2 border-black p-12 text-center text-neutral-500 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div class="font-black text-base text-black mb-1">[ ! ] لا توجد جلسات تدريبية مسجلة باسمك بعد.</div>
                    <p class="text-xs text-neutral-500 mb-4">اضغط على الزر أدناه لإنشاء أول جلسة تدريبية ميدانية.</p>
                    <a href="{{ route('portal.sessions.create') }}" class="px-5 py-2.5 bg-black text-white border-2 border-black hover:bg-neutral-800 font-bold text-xs inline-block">
                        + إنشاء جلسة جديدة الآن
                    </a>
                </div>
            @endforelse
        </div>

        @if($mySessions->hasPages())
            <div class="pt-4">
                {{ $mySessions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
