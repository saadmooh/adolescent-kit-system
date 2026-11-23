<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير حول النشاط - Kit Adolescent</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Tahoma, Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            line-height: 1.5;
        }
        .main-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .section-header {
            font-size: 13pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            color: #000;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }
        .line-item {
            margin-bottom: 6px;
        }
        .dotted {
            border-bottom: 1px dotted #555;
            display: inline-block;
            min-width: 150px;
            padding: 0 5px;
            font-weight: bold;
        }
        .grid-2 {
            display: flex;
            justify-content: space-between;
        }
        .grid-3 {
            display: flex;
            justify-content: space-between;
        }
        .age-box {
            border: 1px solid #000;
            padding: 4px 8px;
            margin: 3px;
            display: inline-block;
            font-size: 10pt;
        }
        .checklist {
            list-style: none;
            padding: 0;
            margin: 6px 0;
        }
        .checklist li {
            margin-bottom: 4px;
        }
        .box-field {
            border: 1px solid #999;
            padding: 6px 10px;
            min-height: 45px;
            margin-bottom: 8px;
            background: #fafafa;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #000000;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 0px;
            border: 2px solid #000000;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 4px 4px 0px 0px rgba(0,0,0,1);
        }
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">🖨️ طباعة تقرير النشاط (Print)</button>

    <div class="main-title">تقرير حول النشاط</div>

    <!-- معلومات حول الميسرين والهيئة المستقبلة -->
    <div class="section-header">معلومات حول الميسرين و الهيئة المستقبلة:</div>
    <div class="line-item">المؤسسة: <span class="dotted" style="min-width: 80%;">{{ $session->institution->name }}</span></div>
    <div class="grid-2 line-item">
        <span>المكان: <span class="dotted" style="min-width: 250px;">{{ $session->venue ?? 'مقر المؤسسة' }}</span></span>
        <span>التاريخ: <span class="dotted" style="min-width: 200px;">{{ $session->session_date->format('d/m/Y') }}</span></span>
    </div>
    <div class="line-item">البلدية: <span class="dotted" style="min-width: 300px;">{{ $session->municipality }}</span></div>

    <div class="line-item"><strong>الميسرين:</strong></div>
    <div class="grid-2 line-item" style="padding-right: 20px;">
        <span>1: <span class="dotted" style="min-width: 200px;">{{ $session->leadFacilitator1->name ?? '.......................' }}</span></span>
        <span>2: <span class="dotted" style="min-width: 200px;">{{ $session->leadFacilitator2->name ?? '.......................' }}</span></span>
    </div>

    <div class="line-item"><strong>الميسرين المساعدين:</strong></div>
    @php $assts = $session->assistant_facilitators ?? []; @endphp
    <div class="grid-2 line-item" style="padding-right: 20px;">
        <span>1: <span class="dotted" style="min-width: 200px;">{{ $assts[0] ?? '.......................' }}</span></span>
        <span>2: <span class="dotted" style="min-width: 200px;">{{ $assts[1] ?? '.......................' }}</span></span>
    </div>
    <div class="grid-2 line-item" style="padding-right: 20px;">
        <span>3: <span class="dotted" style="min-width: 200px;">{{ $assts[2] ?? '.......................' }}</span></span>
        <span>4: <span class="dotted" style="min-width: 200px;">{{ $assts[3] ?? '.......................' }}</span></span>
    </div>
    <div class="grid-2 line-item" style="padding-right: 20px;">
        <span>5: <span class="dotted" style="min-width: 200px;">{{ $assts[4] ?? '.......................' }}</span></span>
        <span>6: <span class="dotted" style="min-width: 200px;">{{ $assts[5] ?? '.......................' }}</span></span>
    </div>

    <div class="line-item">
        الانتقال: من بلدية <span class="dotted">{{ $session->transit_from_municipality ?: $session->municipality }}</span> الى بلدية <span class="dotted">{{ $session->transit_to_municipality ?: $session->municipality }}</span>
    </div>

    <div class="line-item"><strong>جهة الاتصال:</strong></div>
    <div class="line-item" style="padding-right: 20px;">
        الاسم: <span class="dotted" style="min-width: 150px;">{{ $report->host_contact_name }}</span> 
        الهيئة: <span class="dotted" style="min-width: 150px;">{{ $session->institution->name }}</span>
        رقم الهاتف: <span class="dotted" style="min-width: 120px;">{{ $report->host_contact_phone }}</span>
        البريد الالكتروني: <span class="dotted" style="min-width: 150px;">{{ $report->host_contact_email }}</span>
    </div>

    <!-- معلومات حول الفئة المستهدفة -->
    <div class="section-header">معلومات حول الفئة المستهدفة:</div>
    <div class="line-item">
        العدد: <span class="dotted" style="min-width: 60px;">{{ $stats['total_attendees'] }}</span>
        ذكور: <span class="dotted" style="min-width: 60px;">{{ $stats['male_count'] }}</span>
        إناث: <span class="dotted" style="min-width: 60px;">{{ $stats['female_count'] }}</span>
    </div>
    <div class="line-item">
        متمدرس: 
        نعم [ {{ $stats['schooled_count'] > 0 ? 'X' : ' ' }} ] ({{ $stats['schooled_count'] }})
        &nbsp;&nbsp;&nbsp;&nbsp;
        لا [ {{ $stats['unschooled_count'] > 0 ? 'X' : ' ' }} ] ({{ $stats['unschooled_count'] }})
    </div>
    <div class="line-item">
        مستوى دراسي:
        ابتدائي [ {{ $stats['education_levels']['ابتدائي'] ?? 0 }} ] &nbsp;&nbsp;&nbsp;&nbsp;
        متوسط [ {{ $stats['education_levels']['متوسط'] ?? 0 }} ] &nbsp;&nbsp;&nbsp;&nbsp;
        ثانوي [ {{ $stats['education_levels']['ثانوي'] ?? 0 }} ] &nbsp;&nbsp;&nbsp;&nbsp;
        تكوين مهني [ {{ $stats['education_levels']['تكوين مهني'] ?? 0 }} ]
    </div>
    <div class="line-item">
        <strong>معدل العمر:</strong><br>
        <div style="margin-top: 5px;">
            <span class="age-box">من 10 الى 11 ({{ $stats['age_distribution']['من 10 الى 11'] ?? 0 }})</span>
            <span class="age-box">من 11 الى 12 ({{ $stats['age_distribution']['من 11 الى 12'] ?? 0 }})</span>
            <span class="age-box">من 12 الى 13 ({{ $stats['age_distribution']['من 12 الى 13'] ?? 0 }})</span><br>
            <span class="age-box">من 13 الى 14 ({{ $stats['age_distribution']['من 13 الى 14'] ?? 0 }})</span>
            <span class="age-box">من 14 الى 15 ({{ $stats['age_distribution']['من 14 الى 15'] ?? 0 }})</span>
            <span class="age-box">من 15 الى 16 ({{ $stats['age_distribution']['من 15 الى 16'] ?? 0 }})</span><br>
            <span class="age-box">من 16 الى 17 ({{ $stats['age_distribution']['من 16 الى 17'] ?? 0 }})</span>
            <span class="age-box">من 17 الى 18 ({{ $stats['age_distribution']['من 17 الى 18'] ?? 0 }})</span>
            <span class="age-box">من 18 الى 19 ({{ $stats['age_distribution']['من 18 الى 19'] ?? 0 }})</span>
        </div>
    </div>

    <!-- معلومات حول فئة خاصة -->
    <div class="section-header">معلومات حول فئة خاصة:</div>
    <div class="line-item">
        معاق: 
        نعم [ {{ $stats['disabled_count'] > 0 ? 'X' : ' ' }} ] &nbsp;&nbsp;&nbsp;&nbsp;
        لا [ {{ $stats['disabled_count'] == 0 ? 'X' : ' ' }} ]
    </div>
    <div class="line-item">
        نوع الاعاقة: 
        حركية ({{ $stats['disabilities_breakdown']['حركية'] ?? 0 }}) &nbsp;&nbsp;&nbsp;&nbsp;
        بصرية ({{ $stats['disabilities_breakdown']['بصرية'] ?? 0 }}) &nbsp;&nbsp;&nbsp;&nbsp;
        الصم البكم ({{ $stats['disabilities_breakdown']['الصم البكم'] ?? 0 }}) &nbsp;&nbsp;&nbsp;&nbsp;
        شيء آخر ({{ $stats['disabilities_breakdown']['شيء آخر'] ?? 0 }})
    </div>
    <div class="line-item">مشاكل صحية اخرى: <span class="dotted" style="min-width: 70%;">{{ $stats['chronic_diseases_count'] > 0 ? "يوجد {$stats['chronic_diseases_count']} مصابين بأمراض مزمنة" : 'لا توجد' }}</span></div>

    <!-- فاصل الصفحة عند الطباعة -->
    <div class="page-break"></div>

    <!-- معلومات حول محتوى الأنشطة -->
    <div class="section-header">معلومات حول محتوى الأنشطة:</div>
    <div class="grid-2">
        <div style="width: 48%;">
            <strong>النشاط المقدم:</strong>
            @php $selActs = $report->selected_activities ?? []; @endphp
            <ul class="checklist">
                @php
                    $actsList = [
                        'الصمت والأصوات',
                        'كيف نبدو من الداخل والخارج',
                        'جمل تبدأ بكلمة «أنا..." أو " لدي..." أو "أستطيع..."',
                        'ما نقدمه لأنفسنا وللآخرين',
                        'قصص من منظور الحيوانات والحشرات والطيور',
                        'خارطة العلاقات',
                        'كتابة قصة',
                        'بيئتنا',
                        'ممارسة مهارات المقابلة',
                        'جمع القصص',
                        'أفراد عائلتنا وحوار الأسرة، والأصدقاء، والمجتمع'
                    ];
                @endphp
                @foreach($actsList as $act)
                    <li>[ {{ in_array($act, $selActs) ? 'X' : ' ' }} ] {{ $act }}</li>
                @endforeach
            </ul>
        </div>

        <div style="width: 48%;">
            <strong>المهارات / الكفايات المحققة:</strong>
            @php $selSkills = $report->selected_competencies ?? []; @endphp
            <ul class="checklist">
                @php
                    $skillsList = [
                        'التواصل والتعبير',
                        'الهوية والاعنزاز بالنفس',
                        'القيادة والتأثير',
                        'حل المشكلات وإدارة الأزمات',
                        'التعامل مه التوتر وإدارة العواطف',
                        'التعاون والعمل الجماعي',
                        'التعاطف والاحترام',
                        'الأمل في المستقبل وتحديد الأهداف',
                        'التفكير الناقد واتخاذ القرارات',
                        'الابداع والابتكار'
                    ];
                @endphp
                @foreach($skillsList as $sk)
                    <li>[ {{ in_array($sk, $selSkills) ? 'X' : ' ' }} ] {{ $sk }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- معلومات حول سير النشاط -->
    <div class="section-header">معلومات حول سير النشاط:</div>
    <div class="line-item"><strong>التسهيلات:</strong> <div class="box-field">{{ $report->facilities }}</div></div>
    <div class="line-item"><strong>الصعوبات:</strong> <div class="box-field">{{ $report->difficulties }}</div></div>
    <div class="line-item"><strong>نقاط القوة:</strong> <div class="box-field">{{ $report->strengths }}</div></div>
    <div class="line-item"><strong>نقاط الضعف:</strong> <div class="box-field">{{ $report->weaknesses }}</div></div>
    <div class="line-item"><strong>تفاعل اليافعين:</strong> <div class="box-field">{{ $report->engagement_notes }}</div></div>
    <div class="line-item"><strong>الحضور:</strong> <div class="box-field">{{ $report->attendance_notes }}</div></div>
    <div class="line-item"><strong>ملاحظات أخرى:</strong> <div class="box-field">{{ $report->general_remarks }}</div></div>
    <div class="line-item"><strong>توصيات:</strong> <div class="box-field">{{ $report->recommendations }}</div></div>

    <div class="grid-2" style="margin-top: 30px; font-weight: bold;">
        <div>امضاء المقرر: ..............................</div>
        <div>الولاية: <strong>{{ $report->rapporteur_wilaya }}</strong> في: <strong>{{ $report->rapporteur_date ? $report->rapporteur_date->format('d/m/Y') : '...../...../..........' }}</strong></div>
    </div>

    <!-- بطاقة الإشهاد والمصادقة النهائية -->
    <div class="page-break"></div>
    <div class="section-header">معلومات خاصة بالجانب الاعلامي والمصادقة النهائية:</div>
    <div class="line-item"><strong>الصور المطلوبة:</strong></div>
    @php
        $hasGate = $report->mediaAttachments()->where('category', 'institution_gate')->exists();
        $hasTeam = $report->mediaAttachments()->where('category', 'facilitators_team')->exists();
        $hasGroup = $report->mediaAttachments()->where('category', 'group_photo')->exists();
        $hasAction = $report->mediaAttachments()->where('category', 'in_action')->exists();
        $hasVideo = $report->mediaAttachments()->where('category', 'activity_video')->exists();
    @endphp
    <ul class="checklist" style="padding-right: 20px;">
        <li>[ {{ $hasGate ? 'X' : ' ' }} ] الباب الخارجي للمؤسسة التي اجري بها النشاط</li>
        <li>[ {{ $hasTeam ? 'X' : ' ' }} ] طاقم الميسرين</li>
        <li>[ {{ $hasGroup ? 'X' : ' ' }} ] صورة جماعية لليافعين و الهيئة المستقبلة و الميسرين</li>
        <li>[ {{ $hasAction ? 'X' : ' ' }} ] صورة لجميع لليافعين اثناء النشاط</li>
        <li>[ {{ $hasVideo ? 'X' : ' ' }} ] فيديو للنشاط</li>
    </ul>

    <div style="border: 2px solid #000; padding: 15px; margin-top: 25px; line-height: 2;">
        <div style="text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 10px; text-decoration: underline;">
            إشهاد الهيئة المستقبلة
        </div>
        <div>الولاية: <span class="dotted">{{ $session->wilaya }}</span> البلدية: <span class="dotted">{{ $session->municipality }}</span></div>
        <div>الهيئة او المدرسة المستفيدة: <span class="dotted" style="min-width: 60%;">{{ $session->institution->name }}</span></div>
        <div>الفئة: <span class="dotted">{{ $session->institution->category }}</span> الهيئة المستقبلة: <span class="dotted">{{ $session->institution->name }}</span></div>
        <div>جهة الاتصال: الاسم: <span class="dotted">{{ $report->host_contact_name }}</span> الصفة: <span class="dotted">{{ $report->host_contact_title }}</span> الهاتف: <span class="dotted">{{ $report->host_contact_phone }}</span></div>
        <div>التاريخ: <span class="dotted">{{ $session->session_date->format('d/m/Y') }}</span></div>
        <div>الميسرين: <span class="dotted" style="min-width: 70%;">{{ $session->leadFacilitator1->name ?? '' }} - {{ $session->leadFacilitator2->name ?? '' }}</span></div>
        <div>عدد اليافعين: <span class="dotted">{{ $stats['total_attendees'] }}</span> ( الاناث: <span class="dotted">{{ $stats['female_count'] }}</span> / الذكور: <span class="dotted">{{ $stats['male_count'] }}</span> )</div>
        <div>نوع الاعاقات: <span class="dotted">{{ $stats['disabled_count'] > 0 ? "حركية ({$stats['disabilities_breakdown']['حركية']})، بصرية ({$stats['disabilities_breakdown']['بصرية']})" : 'لا توجد' }}</span></div>
        <div>النشاطات: <span class="dotted" style="min-width: 70%;">{{ !empty($report->selected_activities) ? implode('، ', array_slice($report->selected_activities, 0, 4)) : 'أنشطة حقيبة الابتكار' }}</span></div>

        <div class="grid-2" style="margin-top: 30px; font-weight: bold;">
            <div>امضاء الهيئة المستقبلة والختم الرسمي: ..............................</div>
            <div>الولاية: <strong>{{ $report->host_wilaya ?? $session->wilaya }}</strong> في: <strong>{{ $report->host_approval_date ? $report->host_approval_date->format('d/m/Y') : '...../...../..........' }}</strong></div>
        </div>
    </div>

</body>
</html>
