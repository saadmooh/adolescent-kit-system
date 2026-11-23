@extends('layouts.app')

@section('title', 'تقرير حول النشاط - ' . $session->title)

@section('content')
<div class="space-y-6">

    <!-- شريط الترويسة -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-amber-600 mb-1">
                <span>الجلسة التدريبية</span>
                <span>/</span>
                <span>تقرير حول النشاط (Kit Adolescent)</span>
            </div>
            <h1 class="text-xl font-black text-slate-900">تقرير النشاط: {{ $session->title }}</h1>
            <p class="text-xs text-slate-500 mt-1">توليد تلقائي للإحصائيات الديموغرافية، وتوثيق الأنشطة والكفايات المحققة، والأرشفة الإعلامية.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('sessions.attendance.index', $session) }}" class="px-4 py-2.5 bg-sky-50 text-sky-700 hover:bg-sky-100 rounded-xl text-sm font-bold transition flex items-center gap-2">
                <span>📋 كشف الحضور ({{ $stats['total_attendees'] }} حاضر)</span>
            </a>
            <a href="{{ route('sessions.print.report', $session) }}" target="_blank" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>طباعة التقرير الرسمي</span>
            </a>
        </div>
    </div>

    <!-- نموذج تقرير النشاط الكامل -->
    <form action="{{ route('sessions.reports.update', $session) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- القسم 1: معلومات حول الميسرين والهيئة المستقبلة -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2 border-b pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-600"></span>
                <h2 class="font-black text-slate-800 text-base">1. معلومات حول الميسرين و الهيئة المستقبلة</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">المؤسسة:</label>
                    <input type="text" readonly value="{{ $session->institution->name }}" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs text-slate-700 font-bold">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">المكان:</label>
                    <input type="text" readonly value="{{ $session->venue ?? 'مقر المؤسسة' }}" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs text-slate-700">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">التاريخ:</label>
                    <input type="text" readonly value="{{ $session->session_date->format('Y/m/d') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs text-slate-700 font-mono">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">البلدية:</label>
                    <input type="text" readonly value="{{ $session->municipality }}" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs text-slate-700">
                </div>
            </div>

            <!-- الميسرين والمساعدين -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs pt-2">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-800 mb-2">الميسرين الأساسيين:</div>
                    <div class="space-y-1 text-slate-600">
                        <div>1: <strong>{{ $session->leadFacilitator1->name ?? 'غير محدد' }}</strong></div>
                        <div>2: <strong>{{ $session->leadFacilitator2->name ?? 'غير محدد' }}</strong></div>
                    </div>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-800 mb-2">الميسرين المساعدين (حتى 6):</div>
                    <div class="grid grid-cols-2 gap-1 text-slate-600">
                        @php $assistants = $session->assistant_facilitators ?? []; @endphp
                        @for($i = 0; $i < 6; $i++)
                            <div>{{ $i + 1 }}: {{ $assistants[$i] ?? '—' }}</div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- خط الانتقال وجهة الاتصال -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs pt-2">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-800 mb-2">الانتقال:</div>
                    <div class="text-slate-600">
                        من بلدية: <strong>{{ $session->transit_from_municipality ?: $session->municipality }}</strong>
                        إلى بلدية: <strong>{{ $session->transit_to_municipality ?: $session->municipality }}</strong>
                    </div>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-800 mb-2">جهة الاتصال بالمؤسسة:</div>
                    <div class="text-slate-600">
                        <span>الاسم واللقب: <strong>{{ $report->host_contact_name }}</strong></span> | 
                        <span>الصفة: <strong>{{ $report->host_contact_title }}</strong></span> | 
                        <span>الهاتف: <strong>{{ $report->host_contact_phone }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- القسم 2: معلومات حول الفئة المستهدفة (محسوبة تلقائياً 100%) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <h2 class="font-black text-slate-800 text-base">2. معلومات حول الفئة المستهدفة (حساب آلي من كشف الحضور)</h2>
                </div>
                <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-bold">محدث لحظياً</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- الأعداد والنوع -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="text-slate-500 font-bold">العدد الإجمالي: <strong class="text-slate-900 text-sm">{{ $stats['total_attendees'] }}</strong></div>
                    <div class="text-blue-600 font-bold">ذكور: <strong>{{ $stats['male_count'] }}</strong></div>
                    <div class="text-pink-600 font-bold">إناث: <strong>{{ $stats['female_count'] }}</strong></div>
                </div>

                <!-- التمدرس -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="text-slate-500 font-bold">الحالة الدراسية:</div>
                    <div class="text-emerald-700 font-bold">متمدرس: <strong>{{ $stats['schooled_count'] }}</strong></div>
                    <div class="text-amber-700 font-bold">غير متمدرس: <strong>{{ $stats['unschooled_count'] }}</strong></div>
                </div>

                <!-- المستوى الدراسي -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <div class="text-slate-500 font-bold mb-1">المستوى الدراسي:</div>
                    @foreach($stats['education_levels'] as $level => $count)
                        <div class="flex justify-between text-slate-600">
                            <span>{{ $level }}:</span>
                            <span class="font-bold font-mono">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- جدول الفئات العمرية التسع المحددة في الوثيقة الرسمية -->
            <div class="border rounded-xl overflow-hidden mt-3">
                <div class="bg-slate-100 p-2.5 text-xs font-bold text-slate-700">معدل العمر وتوزيع الفئات العمرية:</div>
                <div class="grid grid-cols-3 sm:grid-cols-9 divide-x divide-x-reverse divide-y sm:divide-y-0 text-center text-xs">
                    @foreach($stats['age_distribution'] as $bracket => $count)
                        @if($bracket !== 'أخرى')
                            <div class="p-2.5 bg-white">
                                <div class="text-slate-400 text-[11px]">{{ $bracket }}</div>
                                <div class="text-base font-black text-slate-800 mt-1 font-mono">{{ $count }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- القسم 3: معلومات حول فئة خاصة -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center gap-2 border-b pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                <h2 class="font-black text-slate-800 text-base">3. معلومات حول فئة خاصة وذوي الإعاقة</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div class="p-3 bg-purple-50/50 rounded-xl border border-purple-100">
                    <div class="font-bold text-purple-900 mb-1">إجمالي ذوي الإعاقة:</div>
                    <div class="text-xl font-black text-purple-700 font-mono">{{ $stats['disabled_count'] }}</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="font-semibold text-slate-500 mb-1">إعاقة حركية:</div>
                    <div class="text-lg font-bold text-slate-800 font-mono">{{ $stats['disabilities_breakdown']['حركية'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="font-semibold text-slate-500 mb-1">إعاقة بصرية:</div>
                    <div class="text-lg font-bold text-slate-800 font-mono">{{ $stats['disabilities_breakdown']['بصرية'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="font-semibold text-slate-500 mb-1">الصم البكم / أخرى:</div>
                    <div class="text-lg font-bold text-slate-800 font-mono">
                        {{ ($stats['disabilities_breakdown']['الصم البكم'] ?? 0) + ($stats['disabilities_breakdown']['شيء آخر'] ?? 0) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- القسم 4: محتوى الأنشطة والمهارات المعتمدة في حقيبة الابتكار -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2 border-b pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <h2 class="font-black text-slate-800 text-base">4. معلومات حول محتوى الأنشطة والمهارات المحققة</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- قائمة الأنشطة المقدمة (11 نشاطاً) -->
                <div>
                    <h3 class="font-black text-slate-900 mb-3 flex items-center gap-2">
                        <span>🎯 الأنشطة المقدمة (اختر الأنشطة المنفذة):</span>
                    </h3>
                    <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200 max-h-80 overflow-y-auto">
                        @php $selectedActs = $report->selected_activities ?? []; @endphp
                        @foreach($availableActivities as $act)
                            <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white cursor-pointer transition">
                                <input type="checkbox" name="selected_activities[]" value="{{ $act->title_ar }}"
                                    {{ in_array($act->title_ar, $selectedActs) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                                <span class="font-semibold text-slate-700">{{ $act->title_ar }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- قائمة المهارات والكفايات المحققة (10 كفايات) -->
                <div>
                    <h3 class="font-black text-slate-900 mb-3 flex items-center gap-2">
                        <span>💡 المهارات / الكفايات المحققة:</span>
                    </h3>
                    <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200 max-h-80 overflow-y-auto">
                        @php $selectedSkills = $report->selected_competencies ?? []; @endphp
                        @foreach($availableCompetencies as $comp)
                            <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white cursor-pointer transition">
                                <input type="checkbox" name="selected_competencies[]" value="{{ $comp->title_ar }}"
                                    {{ in_array($comp->title_ar, $selectedSkills) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                <span class="font-semibold text-slate-700">{{ $comp->title_ar }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- القسم 5: سير النشاط (التقييم النوعي) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2 border-b pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                <h2 class="font-black text-slate-800 text-base">5. معلومات حول سير النشاط والتقييم الميداني</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">التسهيلات:</label>
                    <textarea name="facilities" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500" placeholder="ما هي التسهيلات التي قدمتها المؤسسة؟">{{ $report->facilities }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">الصعوبات:</label>
                    <textarea name="difficulties" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500" placeholder="ما هي التحديات أو العوائق التي واجهتكم؟">{{ $report->difficulties }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">نقاط القوة:</label>
                    <textarea name="strengths" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500" placeholder="أبرز العوامل الإيجابية في الجلسة">{{ $report->strengths }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">نقاط الضعف:</label>
                    <textarea name="weaknesses" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500" placeholder="ما يحتاج إلى تحسين في المرات القادمة">{{ $report->weaknesses }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">تفاعل اليافعين:</label>
                    <textarea name="engagement_notes" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500" placeholder="كيف كان تجاوب اليافعين مع الأنشطة؟">{{ $report->engagement_notes }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">الحضور والمواظبة:</label>
                    <textarea name="attendance_notes" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500" placeholder="ملاحظات حول الانضباط والالتزام">{{ $report->attendance_notes }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">ملاحظات أخرى:</label>
                    <textarea name="general_remarks" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500">{{ $report->general_remarks }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">التوصيات:</label>
                    <textarea name="recommendations" rows="3" class="w-full rounded-xl border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500" placeholder="توصيات للمنسقين والجلسات القادمة">{{ $report->recommendations }}</textarea>
                </div>
            </div>

            <!-- توقيع المقرر -->
            <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">المقرر (الميسر المسؤول):</label>
                    <select name="rapporteur_id" class="w-full rounded-xl border-slate-300 text-xs">
                        <option value="">— اختر المقرر —</option>
                        @foreach($facilitators as $f)
                            <option value="{{ $f->id }}" {{ $report->rapporteur_id == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">الولاية:</label>
                    <input type="text" name="rapporteur_wilaya" value="{{ $report->rapporteur_wilaya }}" class="w-full rounded-xl border-slate-300 text-xs">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">التاريخ:</label>
                    <input type="date" name="rapporteur_date" value="{{ $report->rapporteur_date ? $report->rapporteur_date->format('Y-m-d') : '' }}" class="w-full rounded-xl border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <!-- زر الحفظ العائم -->
        <div class="flex items-center justify-end gap-3 sticky bottom-4 bg-white/90 backdrop-blur p-4 rounded-2xl shadow-lg border border-slate-200">
            <button type="submit" class="px-8 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                <span>حفظ تقرير النشاط</span>
            </button>
        </div>
    </form>

    <!-- القسم 6: التوثيق الإعلامي الإلزامي (Checklist) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                <h2 class="font-black text-slate-800 text-base">6. معلومات خاصة بالجانب الإعلامي (قائمة التوثيق الإلزامية)</h2>
            </div>
            <span class="text-xs text-slate-500">4 صور أساسية + فيديو</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $mediaItems = [
                    'institution_gate' => 'الباب الخارجي للمؤسسة التي أجري بها النشاط',
                    'facilitators_team' => 'طاقم الميسرين',
                    'group_photo' => 'صورة جماعية لليافعين والهيئة والميسرين',
                    'in_action' => 'صورة لجميع اليافعين أثناء النشاط',
                    'activity_video' => 'فيديو للنشاط',
                ];
            @endphp

            @foreach($mediaItems as $catKey => $catTitle)
                @php $existingMedia = $report->mediaAttachments()->where('category', $catKey)->first(); @endphp
                <div class="border rounded-xl p-4 bg-slate-50 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-xs text-slate-800">{{ $catTitle }}</span>
                            @if($existingMedia)
                                <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">✓ تم الرفع</span>
                            @else
                                <span class="text-[10px] bg-rose-100 text-rose-800 px-2 py-0.5 rounded-full font-bold">مطلوب</span>
                            @endif
                        </div>

                        @if($existingMedia)
                            <div class="text-[11px] text-slate-500 truncate mt-1">
                                الملف: {{ $existingMedia->file_name }} ({{ round($existingMedia->file_size / 1024) }} KB)
                            </div>
                        @endif
                    </div>

                    <div>
                        @if($existingMedia)
                            <form action="{{ route('media.destroy', $existingMedia) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا المرفق؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold">حذف واستبدال</button>
                            </form>
                        @else
                            <form action="{{ route('reports.media.store', $report) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="category" value="{{ $catKey }}">
                                <input type="file" name="file" required class="text-xs file:py-1 file:px-2 file:rounded-lg file:border-0 file:bg-sky-100 file:text-sky-800 file:font-bold hover:file:bg-sky-200 w-full">
                                <button type="submit" class="px-2.5 py-1 bg-sky-600 text-white rounded-lg text-xs font-bold hover:bg-sky-700">رفع</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
