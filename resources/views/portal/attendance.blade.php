@extends('layouts.app')

@section('title', 'كشف الحضور الميداني - ' . $session->title)

@section('content')
<div class="space-y-6" x-data="attendancePage()">

    <!-- ترويسة كشف الحضور الحادة بالأبيض والأسود -->
    <div class="bg-unicef-dark text-white p-6 border-2 border-unicef-dark flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-[4px_4px_0px_0px_rgba(0,47,108,1)]">
        <div>
            <div class="text-[10px] text-neutral-400 font-mono mb-1">
                بوابة الإدخال الميداني / كشف حضور اليافعين (70 مقعداً)
            </div>
            <h1 class="text-xl font-black">{{ $session->title }}</h1>
            <div class="text-xs text-neutral-300 mt-1 flex flex-wrap gap-4">
                <span>المؤسسة: <strong>{{ $session->institution->name }}</strong></span>
                <span>البلدية: <strong>{{ $session->municipality }} ({{ $session->wilaya }})</strong></span>
                <span>التاريخ: <strong>{{ $session->session_date->format('Y/m/d') }}</strong></span>
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            <button @click="showModal = true" class="px-3.5 py-2 bg-white text-unicef-dark hover:bg-neutral-200 border-2 border-white text-xs font-black transition">
                [ + ] تسجيل يافع جديد
            </button>
            <a href="{{ route('portal.sessions.edit', $session) }}" class="px-3 py-2 bg-unicef-darkBlue text-white border border-white hover:bg-white hover:text-unicef-dark text-xs font-bold transition">
                ✏️ تعديل بيانات الجلسة
            </a>
            <a href="{{ route('portal.sessions.report', $session) }}" class="px-3 py-2 bg-unicef-darkBlue text-white border border-white hover:bg-unicef-darkBlue text-xs font-bold transition">
                الانتقال للتقرير &larr;
            </a>
            <a href="{{ route('sessions.print.attendance', $session) }}" target="_blank" class="px-3 py-2 border border-white text-white hover:bg-white hover:text-unicef-dark text-xs font-bold transition">
                🖨️ طباعة
            </a>
        </div>
    </div>

    <!-- شريط الإحصائيات الفورية بالأبيض والأسود -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3 border-2 border-unicef-dark shadow-[3px_3px_0px_0px_rgba(0,47,108,1)] text-center">
            <div class="text-[10px] text-neutral-500 font-bold uppercase">إجمالي الحضور</div>
            <div class="text-2xl font-black text-unicef-dark mt-0.5 font-mono"><span dir="ltr" class="inline-block" x-text="stats.total_attendees + ' / 70'">{{ $stats['total_attendees'] }} / 70</span></div>
        </div>
        <div class="bg-white p-3 border-2 border-unicef-dark shadow-[3px_3px_0px_0px_rgba(0,47,108,1)] text-center">
            <div class="text-[10px] text-neutral-500 font-bold uppercase">الذكور</div>
            <div class="text-xl font-black text-unicef-dark mt-0.5 font-mono" x-text="stats.male_count + ' (' + stats.male_percentage + '%)'">{{ $stats['male_count'] }} ({{ $stats['male_percentage'] }}%)</div>
        </div>
        <div class="bg-white p-3 border-2 border-unicef-dark shadow-[3px_3px_0px_0px_rgba(0,47,108,1)] text-center">
            <div class="text-[10px] text-neutral-500 font-bold uppercase">الإناث</div>
            <div class="text-xl font-black text-unicef-dark mt-0.5 font-mono" x-text="stats.female_count + ' (' + stats.female_percentage + '%)'">{{ $stats['female_count'] }} ({{ $stats['female_percentage'] }}%)</div>
        </div>
        <div class="bg-white p-3 border-2 border-unicef-dark shadow-[3px_3px_0px_0px_rgba(0,47,108,1)] text-center">
            <div class="text-[10px] text-neutral-500 font-bold uppercase">المتمدرسون</div>
            <div class="text-xl font-black text-unicef-dark mt-0.5 font-mono" x-text="stats.schooled_count">{{ $stats['schooled_count'] }}</div>
        </div>
        <div class="bg-white p-3 border-2 border-unicef-dark shadow-[3px_3px_0px_0px_rgba(0,47,108,1)] text-center">
            <div class="text-[10px] text-neutral-500 font-bold uppercase">أمراض مزمنة</div>
            <div class="text-xl font-black text-unicef-dark mt-0.5 font-mono" x-text="stats.chronic_diseases_count">{{ $stats['chronic_diseases_count'] }}</div>
        </div>
        <div class="bg-white p-3 border-2 border-unicef-dark shadow-[3px_3px_0px_0px_rgba(0,47,108,1)] text-center">
            <div class="text-[10px] text-neutral-500 font-bold uppercase">ذوو إعاقة</div>
            <div class="text-xl font-black text-unicef-dark mt-0.5 font-mono" x-text="stats.disabled_count">{{ $stats['disabled_count'] }}</div>
        </div>
    </div>

    <!-- جدول كشف الحضور الحاد -->
    <div class="bg-white border-2 border-unicef-dark shadow-[6px_6px_0px_0px_rgba(0,47,108,1)] overflow-hidden">
        <div class="p-3 bg-neutral-100 border-b-2 border-unicef-dark flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
            <span class="font-bold text-unicef-dark">قائمة اليافعين المسجلين (وفق جدول استمارة الحضور الرسمية)</span>
            <div class="flex flex-wrap items-center gap-2">
                <input type="text" x-model="searchQuery" placeholder="بحث بالاسم أو البلدية..." class="px-2.5 py-1 text-xs border border-unicef-dark bg-white w-44">
                <select x-model="filterGender" class="px-2 py-1 text-xs border border-unicef-dark bg-white">
                    <option value="">كل الأجناس</option>
                    <option value="ذكر">ذكر</option>
                    <option value="أنثى">أنثى</option>
                </select>
                <select x-model="filterStatus" class="px-2 py-1 text-xs border border-unicef-dark bg-white">
                    <option value="">كل الحالات</option>
                    <option value="present">حاضر فقط</option>
                    <option value="absent">غائب فقط</option>
                </select>
                <button type="button" x-show="searchQuery || filterGender || filterStatus" @click="searchQuery = ''; filterGender = ''; filterStatus = ''" class="px-2 py-1 text-xs font-bold border border-unicef-dark bg-white text-unicef-dark hover:bg-unicef-dark hover:text-white">
                    مسح
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-unicef-dark text-white font-bold border-b-2 border-unicef-dark">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-12">الرقم</th>
                        <th class="py-2.5 px-3">الاسم واللقب</th>
                        <th class="py-2.5 px-3">تاريخ الميلاد (العمر)</th>
                        <th class="py-2.5 px-3">البلدية</th>
                        <th class="py-2.5 px-3">المستوى الدراسي</th>
                        <th class="py-2.5 px-3 text-center">الجنس</th>
                        <th class="py-2.5 px-3">المرض المزمن / الإعاقة</th>
                        <th class="py-2.5 px-3 text-center">حالة الحضور</th>
                        <th class="py-2.5 px-3 text-center">الإجراءات والتحكم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicef-dark font-medium text-unicef-dark">
                    @forelse($attendances as $att)
                        @php $b = $att->beneficiary; @endphp
                        <tr class="hover:bg-neutral-50 transition"
                            :class="{ 'opacity-40 bg-neutral-100': !attendedMap[{{ $att->id }}] }"
                            x-show="matchesFilter(@js($b->full_name), @js($b->municipality), @js($b->gender), {{ $att->id }})">
                            <td class="py-2.5 px-3 text-center font-mono font-bold">
                                {{ sprintf('%02d', $att->serial_number) }}
                            </td>
                            <td class="py-2.5 px-3 font-bold">{{ $b->full_name }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap font-mono">
                                {{ $b->birth_date->format('Y/m/d') }}
                                <span class="font-bold">({{ $b->getAgeAtDate($session->session_date) }} سنة)</span>
                            </td>
                            <td class="py-2.5 px-3">{{ $b->municipality }}</td>
                            <td class="py-2.5 px-3 font-bold">{{ $b->education_level }}</td>
                            <td class="py-2.5 px-3 text-center font-bold">
                                {{ $b->gender }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if($b->has_chronic_disease)
                                    <span class="border border-unicef-dark px-1.5 py-0.5 bg-unicef-dark text-white text-[10px] font-bold">
                                        مزمن: {{ $b->chronic_disease_details ?: 'نعم' }}
                                    </span>
                                @endif
                                @if($b->has_disability)
                                    <span class="border border-unicef-dark px-1.5 py-0.5 bg-neutral-200 text-unicef-dark text-[10px] font-bold mr-1">
                                        إعاقة: {{ $b->disability_type }}
                                    </span>
                                @endif
                                @if(!$b->has_chronic_disease && !$b->has_disability)
                                    <span class="text-neutral-400">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <button type="button"
                                        @click="toggleAttendance({{ $att->id }}, '{{ route('portal.sessions.attendance.toggle', [$session, $att]) }}')"
                                        :disabled="togglingIds[{{ $att->id }}]"
                                        class="border border-unicef-dark px-2.5 py-1 text-xs font-bold transition disabled:opacity-50"
                                        :class="attendedMap[{{ $att->id }}] ? 'bg-unicef-dark text-white hover:bg-unicef-darkBlue' : 'bg-white text-unicef-dark hover:bg-neutral-100'"
                                        x-text="attendedMap[{{ $att->id }}] ? '[ ✓ ] حاضر' : '[ ✗ ] غائب'">
                                    {{ $att->attended ? '[ ✓ ] حاضر' : '[ ✗ ] غائب' }}
                                </button>
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            @click="openEditModal({{ $att->toJson() }}, {{ $b->toJson() }})" 
                                            class="border border-unicef-dark px-2 py-0.5 bg-white text-unicef-dark hover:bg-unicef-dark hover:text-white font-bold transition text-[11px]">
                                        ✏️ تعديل
                                    </button>
                                    <form action="{{ route('portal.sessions.attendance.destroy', [$session, $att]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف اليافع ({{ $b->full_name }}) من كشف الحضور؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="border border-unicef-dark px-1.5 py-0.5 bg-neutral-100 text-unicef-dark hover:bg-unicef-dark hover:text-white font-bold transition text-[11px]" title="حذف اليافع">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-neutral-400 font-bold">
                                لا يوجد يافعون مسجلون بعد. اضغط على زر "تسجيل يافع جديد".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- النافذة المنبثقة الحادة 1: تسجيل يافع جديد -->
    <div x-show="showModal" class="fixed inset-0 z-50 bg-unicef-dark/70 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border-4 border-unicef-dark shadow-[8px_8px_0px_0px_rgba(0,47,108,1)] max-w-2xl w-full max-h-[92vh] overflow-y-auto p-6 space-y-4" @click.away="showModal = false">
            <div class="flex items-center justify-between border-b-2 border-unicef-dark pb-3">
                <div>
                    <h3 class="font-black text-base text-unicef-dark uppercase">[ + ] تسجيل يافع جديد في الجلسة</h3>
                    <p class="text-[10px] text-neutral-600">علاقات حقول ذكية تحسب العمر وتحدد الفئات الصحية والدراسية تلقائياً</p>
                </div>
                <button @click="showModal = false" class="text-unicef-dark font-black text-lg border border-unicef-dark px-2 py-0.5 hover:bg-unicef-dark hover:text-white">✕</button>
            </div>

            <form action="{{ route('portal.sessions.attendance.add', $session) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">الاسم واللقب *</label>
                        <input type="text" name="full_name" required placeholder="مثال: يونس قدور" class="w-full p-2.5 text-xs">
                    </div>

                    <!-- علاقة 1: تاريخ الميلاد -> حساب العمر والفئة العمرية تلقائياً -->
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">تاريخ الميلاد *</label>
                        <input type="date" dir="ltr" name="birth_date" x-model="birthDate" @input="computeAge()" required class="w-full p-2.5 text-xs font-mono">
                        
                        <div x-show="calculatedAge !== null" class="mt-1.5 p-2 border border-unicef-dark bg-neutral-100 flex items-center justify-between">
                            <span>العمر المحسوب: <strong class="font-mono" x-text="calculatedAge + ' سنة'"></strong></span>
                            <span class="px-2 py-0.5 border border-unicef-dark bg-unicef-dark text-white text-[10px] font-bold" x-text="calculatedBracket"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">الجنس *</label>
                        <select name="gender" required class="w-full p-2.5 text-xs">
                            <option value="ذكر">ذكر</option>
                            <option value="أنثى">أنثى</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">البلدية *</label>
                        <input type="text" name="municipality" value="{{ $session->municipality }}" required class="w-full p-2.5 text-xs">
                    </div>

                    <!-- علاقة 2: التمدرس والمستوى الدراسي -->
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">الحالة التعليمية *</label>
                        <select name="is_schooled" required @change="isSchooled = $event.target.value === '1'; onSchooledChange()" class="w-full p-2.5 text-xs">
                            <option value="1" :selected="isSchooled">متمدرس (نعم)</option>
                            <option value="0" :selected="!isSchooled">غير متمدرس (لا)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">المستوى الدراسي *</label>
                        <select name="education_level" x-model="eduLevel" required class="w-full p-2.5 text-xs" :class="{'bg-neutral-100 text-neutral-400': !isSchooled}">
                            <option value="ابتدائي">ابتدائي</option>
                            <option value="متوسط">متوسط</option>
                            <option value="ثانوي">ثانوي</option>
                            <option value="تكوين مهني">تكوين مهني</option>
                            <option value="غير متمدرس" x-show="!isSchooled">غير متمدرس</option>
                        </select>
                    </div>
                </div>

                <!-- علاقة 3: المرض المزمن وتفاصيله المترابطة -->
                <div class="border-t-2 border-unicef-dark pt-3 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="has_chronic_disease" value="0">
                        <input type="checkbox" name="has_chronic_disease" value="1" x-model="hasChronic" class="border border-unicef-dark">
                        <span class="font-bold text-unicef-dark">يعاني اليافع من مرض مزمن؟</span>
                    </label>

                    <div x-show="hasChronic" class="p-3 border border-unicef-dark bg-neutral-50 space-y-1">
                        <label class="block font-bold text-unicef-dark text-[11px]">حدد نوع وتفاصيل المرض المزمن *</label>
                        <input type="text" name="chronic_disease_details" x-model="chronicDetail" :required="hasChronic" placeholder="مثال: ربو، سكري، حساسية..." class="w-full p-2 text-xs">
                    </div>
                </div>

                <!-- علاقة 4: ذوو الإعاقة وتصنيفها المترابط -->
                <div class="border-t-2 border-unicef-dark pt-3 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="has_disability" value="0">
                        <input type="checkbox" name="has_disability" value="1" x-model="hasDisability" class="border border-unicef-dark">
                        <span class="font-bold text-unicef-dark">من فئة ذوي الاحتياجات الخاصة (الإعاقة)؟</span>
                    </label>

                    <div x-show="hasDisability" class="p-3 border border-unicef-dark bg-neutral-50 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-unicef-dark text-[11px] mb-1">نوع الإعاقة الرئيسية *</label>
                                <select name="disability_type" x-model="disabilityType" :required="hasDisability" class="w-full p-2 text-xs">
                                    <option value="حركية">إعاقة حركية</option>
                                    <option value="بصرية">إعاقة بصرية</option>
                                    <option value="الصم البكم">الصم البكم</option>
                                    <option value="شيء آخر">شيء آخر (تحديد مخصص)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-unicef-dark text-[11px] mb-1">تفاصيل إضافية حول الإعاقة</label>
                                <input type="text" name="disability_details" x-model="disabilityDetail" placeholder="مثال: شلل طرف سفلي..." class="w-full p-2 text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-unicef-dark">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-unicef-dark text-unicef-dark hover:bg-neutral-100 font-bold">إلغاء</button>
                    <button type="submit" class="px-6 py-2 bg-unicef-dark text-white hover:bg-unicef-darkBlue font-black uppercase border border-unicef-dark">
                        حفظ وتأكيد الحضور
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- النافذة المنبثقة الحادة 2: تعديل بيانات اليافع المسجل (Edit Modal) -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 bg-unicef-dark/70 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border-4 border-unicef-dark shadow-[8px_8px_0px_0px_rgba(0,47,108,1)] max-w-2xl w-full max-h-[92vh] overflow-y-auto p-6 space-y-4" @click.away="showEditModal = false">
            <div class="flex items-center justify-between border-b-2 border-unicef-dark pb-3">
                <div>
                    <h3 class="font-black text-base text-unicef-dark uppercase">✏️ تعديل بيانات اليافع المستفيد</h3>
                    <p class="text-[10px] text-neutral-600">تحديث البيانات الديموغرافية، التعليمية والصحية لليافع</p>
                </div>
                <button @click="showEditModal = false" class="text-unicef-dark font-black text-lg border border-unicef-dark px-2 py-0.5 hover:bg-unicef-dark hover:text-white">✕</button>
            </div>

            <form :action="editActionUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">الاسم واللقب *</label>
                        <input type="text" name="full_name" x-model="editFullName" required class="w-full p-2.5 text-xs font-bold">
                    </div>

                    <!-- تاريخ الميلاد -> حساب العمر آلياً -->
                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">تاريخ الميلاد *</label>
                        <input type="date" dir="ltr" name="birth_date" x-model="editBirthDate" @input="computeEditAge()" required class="w-full p-2.5 text-xs font-mono">
                        
                        <div x-show="editCalculatedAge !== null" class="mt-1.5 p-2 border border-unicef-dark bg-neutral-100 flex items-center justify-between">
                            <span>العمر المحسوب: <strong class="font-mono" x-text="editCalculatedAge + ' سنة'"></strong></span>
                            <span class="px-2 py-0.5 border border-unicef-dark bg-unicef-dark text-white text-[10px] font-bold" x-text="editCalculatedBracket"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">الجنس *</label>
                        <select name="gender" x-model="editGender" required class="w-full p-2.5 text-xs">
                            <option value="ذكر">ذكر</option>
                            <option value="أنثى">أنثى</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">البلدية *</label>
                        <input type="text" name="municipality" x-model="editMunicipality" required class="w-full p-2.5 text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">الحالة التعليمية *</label>
                        <select name="is_schooled" required @change="editIsSchooled = $event.target.value === '1'; onEditSchooledChange()" class="w-full p-2.5 text-xs">
                            <option value="1" :selected="editIsSchooled">متمدرس (نعم)</option>
                            <option value="0" :selected="!editIsSchooled">غير متمدرس (لا)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">المستوى الدراسي *</label>
                        <select name="education_level" x-model="editEduLevel" required class="w-full p-2.5 text-xs" :class="{'bg-neutral-100 text-neutral-400': !editIsSchooled}">
                            <option value="ابتدائي">ابتدائي</option>
                            <option value="متوسط">متوسط</option>
                            <option value="ثانوي">ثانوي</option>
                            <option value="تكوين مهني">تكوين مهني</option>
                            <option value="غير متمدرس" x-show="!editIsSchooled">غير متمدرس</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-unicef-dark mb-1">رقم الترتيب في الكشف (الرقم التسلسلي 1 - 70):</label>
                        <input type="number" name="serial_number" x-model="editSerial" min="1" max="70" class="w-full p-2.5 text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- المرض المزمن -->
                <div class="border-t-2 border-unicef-dark pt-3 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="has_chronic_disease" value="0">
                        <input type="checkbox" name="has_chronic_disease" value="1" x-model="editHasChronic" class="border border-unicef-dark">
                        <span class="font-bold text-unicef-dark">يعاني اليافع من مرض مزمن؟</span>
                    </label>

                    <div x-show="editHasChronic" class="p-3 border border-unicef-dark bg-neutral-50 space-y-1">
                        <label class="block font-bold text-unicef-dark text-[11px]">حدد نوع وتفاصيل المرض المزمن *</label>
                        <input type="text" name="chronic_disease_details" x-model="editChronicDetail" :required="editHasChronic" class="w-full p-2 text-xs">
                    </div>
                </div>

                <!-- الإعاقة -->
                <div class="border-t-2 border-unicef-dark pt-3 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="has_disability" value="0">
                        <input type="checkbox" name="has_disability" value="1" x-model="editHasDisability" class="border border-unicef-dark">
                        <span class="font-bold text-unicef-dark">من فئة ذوي الاحتياجات الخاصة (الإعاقة)؟</span>
                    </label>

                    <div x-show="editHasDisability" class="p-3 border border-unicef-dark bg-neutral-50 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-unicef-dark text-[11px] mb-1">نوع الإعاقة الرئيسية *</label>
                                <select name="disability_type" x-model="editDisabilityType" :required="editHasDisability" class="w-full p-2 text-xs">
                                    <option value="حركية">إعاقة حركية</option>
                                    <option value="بصرية">إعاقة بصرية</option>
                                    <option value="الصم البكم">الصم البكم</option>
                                    <option value="شيء آخر">شيء آخر (تحديد مخصص)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-unicef-dark text-[11px] mb-1">تفاصيل إضافية حول الإعاقة</label>
                                <input type="text" name="disability_details" x-model="editDisabilityDetail" class="w-full p-2 text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-unicef-dark">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-unicef-dark text-unicef-dark hover:bg-neutral-100 font-bold">إلغاء</button>
                    <button type="submit" class="px-6 py-2 bg-unicef-dark text-white hover:bg-unicef-darkBlue font-black uppercase border border-unicef-dark">
                        [ ✓ ] حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function attendancePage() {
    return {
        stats: @json($stats),
        attendedMap: @json($attendances->pluck('attended', 'id')),
        togglingIds: {},
        searchQuery: '',
        filterGender: '',
        filterStatus: '',

        async toggleAttendance(attId, url) {
            if (this.togglingIds[attId]) return;
            this.togglingIds[attId] = true;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                if (data.success) {
                    this.attendedMap[attId] = Boolean(data.attended);
                    if (data.stats) this.stats = data.stats;
                }
            } catch (e) {
                alert('تعذر تحديث حالة الحضور، يرجى المحاولة مرة أخرى.');
            } finally {
                this.togglingIds[attId] = false;
            }
        },

        matchesFilter(name, municipality, gender, attId) {
            const q = this.searchQuery.trim().toLowerCase();
            if (q && !name.toLowerCase().includes(q) && !municipality.toLowerCase().includes(q)) return false;
            if (this.filterGender && gender !== this.filterGender) return false;
            if (this.filterStatus === 'present' && !this.attendedMap[attId]) return false;
            if (this.filterStatus === 'absent' && this.attendedMap[attId]) return false;
            return true;
        },

        showModal: false,
        birthDate: '',
        calculatedAge: null,
        calculatedBracket: '',
        isSchooled: true,
        eduLevel: 'متوسط',
        hasChronic: false,
        chronicDetail: '',
        hasDisability: false,
        disabilityType: 'حركية',
        disabilityDetail: '',

        showEditModal: false,
        editActionUrl: '',
        editId: null,
        editFullName: '',
        editBirthDate: '',
        editCalculatedAge: null,
        editCalculatedBracket: '',
        editGender: 'ذكر',
        editMunicipality: '',
        editIsSchooled: true,
        editEduLevel: 'متوسط',
        editHasChronic: false,
        editChronicDetail: '',
        editHasDisability: false,
        editDisabilityType: 'حركية',
        editDisabilityDetail: '',
        editSerial: 1,

        openEditModal(att, b) {
            this.editActionUrl = '/portal/sessions/{{ $session->id }}/beneficiaries/' + b.id;
            this.editId = b.id;
            this.editFullName = b.full_name;
            this.editBirthDate = b.birth_date ? b.birth_date.substring(0, 10) : '';
            this.editGender = b.gender;
            this.editMunicipality = b.municipality;
            this.editIsSchooled = Boolean(b.is_schooled);
            this.editEduLevel = b.education_level || 'متوسط';
            this.editHasChronic = Boolean(b.has_chronic_disease);
            this.editChronicDetail = b.chronic_disease_details || '';
            this.editHasDisability = Boolean(b.has_disability);
            this.editDisabilityType = b.disability_type || 'حركية';
            this.editDisabilityDetail = b.disability_details || '';
            this.editSerial = att.serial_number;
            this.computeEditAge();
            this.showEditModal = true;
        },

        computeAge() {
            if (!this.birthDate) {
                this.calculatedAge = null;
                this.calculatedBracket = '';
                return;
            }
            const bDate = new Date(this.birthDate);
            const refDate = new Date('{{ $session->session_date->format('Y-m-d') }}');
            let age = refDate.getFullYear() - bDate.getFullYear();
            const m = refDate.getMonth() - bDate.getMonth();
            if (m < 0 || (m === 0 && refDate.getDate() < bDate.getDate())) {
                age--;
            }
            this.calculatedAge = age;

            if (age < 10) this.calculatedBracket = 'أقل من 10';
            else if (age >= 10 && age < 11) this.calculatedBracket = 'من 10 الى 11';
            else if (age >= 11 && age < 12) this.calculatedBracket = 'من 11 الى 12';
            else if (age >= 12 && age < 13) this.calculatedBracket = 'من 12 الى 13';
            else if (age >= 13 && age < 14) this.calculatedBracket = 'من 13 الى 14';
            else if (age >= 14 && age < 15) this.calculatedBracket = 'من 14 الى 15';
            else if (age >= 15 && age < 16) this.calculatedBracket = 'من 15 الى 16';
            else if (age >= 16 && age < 17) this.calculatedBracket = 'من 16 الى 17';
            else if (age >= 17 && age < 18) this.calculatedBracket = 'من 17 الى 18';
            else if (age >= 18 && age <= 19) this.calculatedBracket = 'من 18 الى 19';
            else this.calculatedBracket = 'أكثر من 19';
        },

        computeEditAge() {
            if (!this.editBirthDate) {
                this.editCalculatedAge = null;
                this.editCalculatedBracket = '';
                return;
            }
            const bDate = new Date(this.editBirthDate);
            const refDate = new Date('{{ $session->session_date->format('Y-m-d') }}');
            let age = refDate.getFullYear() - bDate.getFullYear();
            const m = refDate.getMonth() - bDate.getMonth();
            if (m < 0 || (m === 0 && refDate.getDate() < bDate.getDate())) {
                age--;
            }
            this.editCalculatedAge = age;

            if (age < 10) this.editCalculatedBracket = 'أقل من 10';
            else if (age >= 10 && age < 11) this.editCalculatedBracket = 'من 10 الى 11';
            else if (age >= 11 && age < 12) this.editCalculatedBracket = 'من 11 الى 12';
            else if (age >= 12 && age < 13) this.editCalculatedBracket = 'من 12 الى 13';
            else if (age >= 13 && age < 14) this.editCalculatedBracket = 'من 13 الى 14';
            else if (age >= 14 && age < 15) this.editCalculatedBracket = 'من 14 الى 15';
            else if (age >= 15 && age < 16) this.editCalculatedBracket = 'من 15 الى 16';
            else if (age >= 16 && age < 17) this.editCalculatedBracket = 'من 16 الى 17';
            else if (age >= 17 && age < 18) this.editCalculatedBracket = 'من 17 الى 18';
            else if (age >= 18 && age <= 19) this.editCalculatedBracket = 'من 18 الى 19';
            else this.editCalculatedBracket = 'أكثر من 19';
        },

        onSchooledChange() {
            if (!this.isSchooled) {
                this.eduLevel = 'غير متمدرس';
            } else if (this.eduLevel === 'غير متمدرس') {
                this.eduLevel = 'متوسط';
            }
        },

        onEditSchooledChange() {
            if (!this.editIsSchooled) {
                this.editEduLevel = 'غير متمدرس';
            } else if (this.editEduLevel === 'غير متمدرس') {
                this.editEduLevel = 'متوسط';
            }
        }
    };
}
</script>
@endpush
