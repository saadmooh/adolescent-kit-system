@extends('layouts.app')

@section('title', 'تقرير النشاط الميداني - ' . $session->title)

@section('content')
<div class="space-y-6">

    <!-- الترويسة الحادة للتقرير بالأبيض والأسود -->
    <div class="bg-unicef-dark text-white p-6 border-2 border-unicef-dark flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-[4px_4px_0px_0px_rgba(0,47,108,1)]">
        <div>
            <div class="text-[10px] text-neutral-400 font-mono mb-1">
                بوابة الإدخال الميداني / تقرير النشاط (حقيبة الابتكار Kit Adolescent)
            </div>
            <h1 class="text-xl font-black">{{ $session->title }}</h1>
            <p class="text-xs text-neutral-300 mt-1">تعبئة البيانات النوعية، تحديد الأنشطة والكفايات، وإرفاق التوثيق الإعلامي الإلزامي.</p>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            <a href="{{ route('portal.sessions.edit', $session) }}" class="px-3.5 py-2 bg-unicef-darkBlue text-white border border-white hover:bg-white hover:text-unicef-dark text-xs font-bold transition">
                ✏️ تعديل بيانات الجلسة
            </a>
            <a href="{{ route('portal.sessions.attendance', $session) }}" class="px-4 py-2 bg-white text-unicef-dark hover:bg-neutral-200 border-2 border-white text-xs font-bold transition">
                كشف الحضور ({{ $stats['total_attendees'] }} حاضر)
            </a>
            <a href="{{ route('sessions.print.report', $session) }}" target="_blank" class="px-4 py-2 border border-white text-white hover:bg-white hover:text-unicef-dark text-xs font-bold transition">
                🖨️ طباعة التقرير الرسمي
            </a>
        </div>
    </div>

    <!-- نموذج تقرير النشاط الحاد -->
    <form action="{{ route('portal.sessions.report.update', $session) }}" method="POST" class="space-y-6"
          x-data="{
              isDirty: false,
              selectedActsCount: {{ count($report->selected_activities ?? []) }},
              selectedSkillsCount: {{ count($report->selected_competencies ?? []) }},
              updateCounts() {
                  this.selectedActsCount = this.$el.querySelectorAll('input[name=\'selected_activities[]\']:checked').length;
                  this.selectedSkillsCount = this.$el.querySelectorAll('input[name=\'selected_competencies[]\']:checked').length;
              }
          }"
          @input="isDirty = true"
          @change="isDirty = true; updateCounts()"
          @submit="isDirty = false"
          @beforeunload.window="if (isDirty) $event.preventDefault()">
        @csrf
        @method('PUT')

        <!-- 1. معلومات حول الميسرين والهيئة المستقبلة -->
        <div class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-4">
            <div class="border-b-2 border-unicef-dark pb-2">
                <h2 class="font-black text-sm text-unicef-dark uppercase">1. معلومات حول الميسرين و الهيئة المستقبلة</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="font-bold text-unicef-dark block mb-1">المؤسسة:</label>
                    <input type="text" readonly value="{{ $session->institution->name }}" class="w-full bg-neutral-100 p-2.5 text-xs font-bold">
                </div>
                <div>
                    <label class="font-bold text-unicef-dark block mb-1">المكان:</label>
                    <input type="text" readonly value="{{ $session->venue ?? 'مقر المؤسسة' }}" class="w-full bg-neutral-100 p-2.5 text-xs">
                </div>
                <div>
                    <label class="font-bold text-unicef-dark block mb-1">التاريخ:</label>
                    <input type="text" readonly value="{{ $session->session_date->format('Y/m/d') }}" class="w-full bg-neutral-100 p-2.5 text-xs font-mono">
                </div>
                <div>
                    <label class="font-bold text-unicef-dark block mb-1">البلدية:</label>
                    <input type="text" readonly value="{{ $session->municipality }}" class="w-full bg-neutral-100 p-2.5 text-xs">
                </div>
            </div>

            <!-- الميسرين والمساعدين -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
                <div class="bg-neutral-50 p-4 border border-unicef-dark">
                    <div class="font-black text-unicef-dark mb-2 uppercase">طاقم الميسرين الأساسيين:</div>
                    <div class="space-y-1 text-unicef-dark">
                        <div>1: <strong>{{ $session->leadFacilitator1->name ?? 'غير محدد' }}</strong></div>
                        <div>2: <strong>{{ $session->leadFacilitator2->name ?? 'غير محدد' }}</strong></div>
                    </div>
                </div>

                <div class="bg-neutral-50 p-4 border border-unicef-dark">
                    <div class="font-black text-unicef-dark mb-2 uppercase">الميسرين المساعدين (1 إلى 6):</div>
                    <div class="grid grid-cols-2 gap-1 text-unicef-dark">
                        @php $assistants = $session->assistant_facilitators ?? []; @endphp
                        @for($i = 0; $i < 6; $i++)
                            <div>{{ $i + 1 }}: {{ $assistants[$i] ?? '—' }}</div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- مسار الانتقال وجهة الاتصال -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
                <div class="bg-neutral-50 p-4 border border-unicef-dark">
                    <div class="font-black text-unicef-dark mb-1 uppercase">مسار الانتقال الميداني:</div>
                    <div>من بلدية: <strong>{{ $session->transit_from_municipality ?: $session->municipality }}</strong></div>
                    <div>إلى بلدية: <strong>{{ $session->transit_to_municipality ?: $session->municipality }}</strong></div>
                </div>

                <div class="bg-neutral-50 p-4 border border-unicef-dark">
                    <div class="font-black text-unicef-dark mb-1 uppercase">جهة الاتصال بالمؤسسة:</div>
                    <div>الاسم: <strong>{{ $report->host_contact_name }}</strong> ({{ $report->host_contact_title }})</div>
                    <div>الهاتف: <strong class="font-mono">{{ $report->host_contact_phone }}</strong></div>
                </div>
            </div>
        </div>

        <!-- 2. الفئة المستهدفة المحسوبة تلقائياً 100% -->
        <div class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-4">
            <div class="flex items-center justify-between border-b-2 border-unicef-dark pb-2">
                <h2 class="font-black text-sm text-unicef-dark uppercase">2. معلومات حول الفئة المستهدفة (محسوبة آلياً من كشف الحضور)</h2>
                <span class="border border-unicef-dark px-2 py-0.5 font-mono text-[10px] font-bold bg-neutral-100">تحديث لحظي</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3 border border-unicef-dark bg-neutral-50 space-y-1">
                    <div class="font-bold">العدد الإجمالي: <strong class="font-mono">{{ $stats['total_attendees'] }}</strong></div>
                    <div>ذكور: <strong class="font-mono">{{ $stats['male_count'] }}</strong> ({{ $stats['male_percentage'] }}%)</div>
                    <div>إناث: <strong class="font-mono">{{ $stats['female_count'] }}</strong> ({{ $stats['female_percentage'] }}%)</div>
                </div>

                <div class="p-3 border border-unicef-dark bg-neutral-50 space-y-1">
                    <div class="font-bold">حالة التمدرس:</div>
                    <div>متمدرس: <strong class="font-mono">{{ $stats['schooled_count'] }}</strong></div>
                    <div>غير متمدرس: <strong class="font-mono">{{ $stats['unschooled_count'] }}</strong></div>
                </div>

                <div class="p-3 border border-unicef-dark bg-neutral-50 space-y-0.5">
                    <div class="font-bold mb-1">المستويات الدراسية:</div>
                    @foreach($stats['education_levels'] as $level => $count)
                        <div class="flex justify-between">
                            <span>{{ $level }}:</span>
                            <span class="font-bold font-mono">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- جدول الفئات العمرية التسع بالأبيض والأسود -->
            <div class="border-2 border-unicef-dark overflow-hidden mt-3">
                <div class="bg-neutral-100 p-2 text-xs font-bold border-b border-unicef-dark">توزيع الفئات العمرية التسع المعتمدة بالتقرير:</div>
                <div class="grid grid-cols-3 sm:grid-cols-9 divide-x divide-x-reverse divide-y sm:divide-y-0 text-center text-xs divide-unicef-dark">
                    @foreach($stats['age_distribution'] as $bracket => $count)
                        @if($bracket !== 'أخرى')
                            <div class="p-2.5 bg-white">
                                <div class="text-[10px] text-neutral-600 font-bold">{{ $bracket }}</div>
                                <div class="text-base font-black text-unicef-dark mt-1 font-mono">{{ $count }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 3. فئات خاصة وذوي الإعاقة -->
        <div class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-3">
            <div class="border-b-2 border-unicef-dark pb-2">
                <h2 class="font-black text-sm text-unicef-dark uppercase">3. معلومات حول فئة خاصة وذوي الإعاقة</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div class="p-3 border border-unicef-dark bg-unicef-dark text-white">
                    <div class="text-[11px] font-bold uppercase">إجمالي ذوي الإعاقة:</div>
                    <div class="text-2xl font-black font-mono mt-1">{{ $stats['disabled_count'] }}</div>
                </div>
                <div class="p-3 border border-unicef-dark bg-neutral-50">
                    <div class="text-neutral-600 font-bold mb-1">إعاقة حركية:</div>
                    <div class="text-lg font-bold font-mono">{{ $stats['disabilities_breakdown']['حركية'] ?? 0 }}</div>
                </div>
                <div class="p-3 border border-unicef-dark bg-neutral-50">
                    <div class="text-neutral-600 font-bold mb-1">إعاقة بصرية:</div>
                    <div class="text-lg font-bold font-mono">{{ $stats['disabilities_breakdown']['بصرية'] ?? 0 }}</div>
                </div>
                <div class="p-3 border border-unicef-dark bg-neutral-50">
                    <div class="text-neutral-600 font-bold mb-1">الصم البكم / أخرى:</div>
                    <div class="text-lg font-bold font-mono">
                        {{ ($stats['disabilities_breakdown']['الصم البكم'] ?? 0) + ($stats['disabilities_breakdown']['شيء آخر'] ?? 0) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. محتوى الأنشطة والكفايات المعتمدة في حقيبة الابتكار -->
        <div class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-4">
            <div class="border-b-2 border-unicef-dark pb-2">
                <h2 class="font-black text-sm text-unicef-dark uppercase">4. محتوى الأنشطة والمهارات المحققة (Kit Adolescent)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <div>
                    <h3 class="font-black text-unicef-dark mb-2 uppercase flex items-center justify-between">
                        <span>[ الأنشطة المقدمة - اختر المنفذ منها ]:</span>
                        <span class="px-2 py-0.5 bg-unicef-light border border-unicef-dark text-[11px] font-mono" x-text="selectedActsCount + ' محدد'"></span>
                    </h3>
                    <div class="space-y-1.5 bg-neutral-50 p-4 border border-unicef-dark max-h-72 overflow-y-auto">
                        @php $selectedActs = $report->selected_activities ?? []; @endphp
                        @foreach($availableActivities as $act)
                            <label class="flex items-center gap-2 p-1 border border-neutral-300 hover:border-unicef-dark bg-white cursor-pointer transition">
                                <input type="checkbox" name="selected_activities[]" value="{{ $act->title_ar }}"
                                    {{ in_array($act->title_ar, $selectedActs) ? 'checked' : '' }}
                                    class="border border-unicef-dark">
                                <span class="font-bold text-unicef-dark">{{ $act->title_ar }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="font-black text-unicef-dark mb-2 uppercase flex items-center justify-between">
                        <span>[ المهارات والكفايات المحققة ]:</span>
                        <span class="px-2 py-0.5 bg-unicef-light border border-unicef-dark text-[11px] font-mono" x-text="selectedSkillsCount + ' محدد'"></span>
                    </h3>
                    <div class="space-y-1.5 bg-neutral-50 p-4 border border-unicef-dark max-h-72 overflow-y-auto">
                        @php $selectedSkills = $report->selected_competencies ?? []; @endphp
                        @foreach($availableCompetencies as $comp)
                            <label class="flex items-center gap-2 p-1 border border-neutral-300 hover:border-unicef-dark bg-white cursor-pointer transition">
                                <input type="checkbox" name="selected_competencies[]" value="{{ $comp->title_ar }}"
                                    {{ in_array($comp->title_ar, $selectedSkills) ? 'checked' : '' }}
                                    class="border border-unicef-dark">
                                <span class="font-bold text-unicef-dark">{{ $comp->title_ar }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. سير النشاط والتقييم النوعي -->
        <div class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-4">
            <div class="border-b-2 border-unicef-dark pb-2">
                <h2 class="font-black text-sm text-unicef-dark uppercase">5. سير النشاط والتقييم الميداني</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="font-bold text-unicef-dark block mb-1">التسهيلات المقدمة:</label>
                    <textarea name="facilities" rows="3" class="w-full p-2.5 text-xs">{{ $report->facilities }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-unicef-dark block mb-1">الصعوبات والعوائق:</label>
                    <textarea name="difficulties" rows="3" class="w-full p-2.5 text-xs">{{ $report->difficulties }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-unicef-dark block mb-1">نقاط القوة:</label>
                    <textarea name="strengths" rows="3" class="w-full p-2.5 text-xs">{{ $report->strengths }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-unicef-dark block mb-1">نقاط الضعف:</label>
                    <textarea name="weaknesses" rows="3" class="w-full p-2.5 text-xs">{{ $report->weaknesses }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-unicef-dark block mb-1">تفاعل اليافعين:</label>
                    <textarea name="engagement_notes" rows="3" class="w-full p-2.5 text-xs">{{ $report->engagement_notes }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-unicef-dark block mb-1">ملاحظات الحضور:</label>
                    <textarea name="attendance_notes" rows="3" class="w-full p-2.5 text-xs">{{ $report->attendance_notes }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-unicef-dark block mb-1">ملاحظات أخرى:</label>
                    <textarea name="general_remarks" rows="3" class="w-full p-2.5 text-xs">{{ $report->general_remarks }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-unicef-dark block mb-1">التوصيات والمقترحات:</label>
                    <textarea name="recommendations" rows="3" class="w-full p-2.5 text-xs">{{ $report->recommendations }}</textarea>
                </div>
            </div>

            <div class="border-t-2 border-unicef-dark pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="font-bold text-unicef-dark block mb-1">ولاية المقرر:</label>
                    <input type="text" name="rapporteur_wilaya" value="{{ $report->rapporteur_wilaya ?: $session->wilaya }}" class="w-full p-2.5 text-xs">
                </div>
                <div>
                    <label class="font-bold text-unicef-dark block mb-1">تاريخ توقيع التقرير:</label>
                    <input type="date" name="rapporteur_date" value="{{ $report->rapporteur_date ? $report->rapporteur_date->format('Y-m-d') : date('Y-m-d') }}" class="w-full p-2.5 text-xs font-mono">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 mt-6 bg-white p-4 border-2 border-unicef-dark shadow-[6px_6px_0px_0px_rgba(0,47,108,1)]">
            <div>
                <span x-show="isDirty" style="display: none;" class="text-xs font-bold text-amber-800 bg-amber-50 border border-amber-400 px-3 py-1.5 inline-block">
                    ⚠️ لديك تغييرات غير محفوظة
                </span>
            </div>
            <button type="submit" class="px-8 py-3 bg-unicef-dark text-white hover:bg-unicef-darkBlue font-black text-xs uppercase border-2 border-unicef-dark transition">
                [ ✓ ] حفظ بيانات التقرير الميداني
            </button>
        </div>
    </form>

    <!-- 6. قائمة التوثيق الإعلامي الإلزامية الحادة -->
    <div class="bg-white border-2 border-unicef-dark p-6 shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] space-y-4">
        <div class="flex items-center justify-between border-b-2 border-unicef-dark pb-2">
            <h2 class="font-black text-sm text-unicef-dark uppercase">6. قائمة التوثيق الإعلامي الإلزامية (الصور والفيديو)</h2>
            <span class="text-xs text-neutral-500 font-mono">4 صور + فيديو</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $mediaItems = [
                    'institution_gate' => 'الباب الخارجي للمؤسسة التي أجري بها النشاط',
                    'facilitators_team' => 'طاقم الميسرين',
                    'group_photo' => 'صورة جماعية لليافعين والهيئة والميسرين',
                    'in_action' => 'صورة لجميع اليافعين أثناء النشاط',
                    'activity_video' => 'فيديو النشاط',
                ];
            @endphp

            @foreach($mediaItems as $catKey => $catTitle)
                @php $existingMedia = $report->mediaAttachments()->where('category', $catKey)->first(); @endphp
                <div class="border border-unicef-dark p-4 bg-neutral-50 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-xs text-unicef-dark">{{ $catTitle }}</span>
                            @if($existingMedia)
                                <span class="border border-unicef-dark bg-unicef-dark text-white px-1.5 py-0.5 text-[10px] font-bold">✓ تم الرفع</span>
                            @else
                                <span class="border border-unicef-dark bg-white text-unicef-dark px-1.5 py-0.5 text-[10px] font-bold">مطلوب</span>
                            @endif
                        </div>

                        @if($existingMedia)
                            <div class="text-[11px] font-mono text-neutral-600 truncate mt-1">
                                {{ $existingMedia->file_name }} ({{ round($existingMedia->file_size / 1024) }} KB)
                            </div>
                            @if(str_starts_with($existingMedia->mime_type, 'image/'))
                                <div class="mt-2 border border-unicef-dark overflow-hidden bg-neutral-100">
                                    <a href="{{ asset('storage/' . $existingMedia->file_path) }}" target="_blank" title="انقر لفتح الصورة بالحجم الكامل">
                                        <img src="{{ asset('storage/' . $existingMedia->file_path) }}" alt="{{ $catTitle }}" class="w-full h-28 object-cover hover:opacity-80 transition">
                                    </a>
                                </div>
                            @elseif(str_starts_with($existingMedia->mime_type, 'video/'))
                                <div class="mt-2 border border-unicef-dark p-2 bg-neutral-100 flex items-center justify-between text-xs">
                                    <span>🎬 ملف فيديو</span>
                                    <a href="{{ asset('storage/' . $existingMedia->file_path) }}" target="_blank" class="underline font-bold text-unicef-dark">
                                        تشغيل / معاينة &larr;
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div>
                        @if($existingMedia)
                            <form action="{{ route('media.destroy', $existingMedia) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد حذف واستبدال هذا المرفق؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-unicef-dark underline font-bold hover:bg-unicef-dark hover:text-white px-1">
                                    [ حذف واستبدال ]
                                </button>
                            </form>
                        @else
                            <form action="{{ route('reports.media.store', $report) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="category" value="{{ $catKey }}">
                                <input type="file" name="file" required class="text-xs file:py-1 file:px-2 file:border file:border-unicef-dark file:bg-white file:text-unicef-dark file:font-bold hover:file:bg-unicef-dark hover:file:text-white w-full">
                                <button type="submit" class="px-3 py-1 bg-unicef-dark text-white hover:bg-unicef-darkBlue text-xs font-bold border border-unicef-dark">رفع</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
