@extends('layouts.app')

@section('title', 'المنصة الوطنية لحقيبة الابتكار والتعبير لليافعين')

@section('content')
<div class="space-y-10">

    <!-- القسم التعريفي الرئيسي (Hero Section) - أبيض وأسود حاد -->
    <section class="bg-black text-white p-8 sm:p-12 border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-6">
        <div class="flex flex-wrap items-center gap-3 text-xs font-mono">
            <span class="px-2.5 py-1 bg-white text-black font-bold uppercase tracking-wider">منهجية معتمدة</span>
            <span class="text-neutral-400">قطاع الشباب والرياضة &times; منظمة اليونيسف (UNICEF)</span>
            <span class="text-neutral-600">|</span>
            <span class="text-neutral-300">منظومة المتابعة والتقييم M&E</span>
        </div>

        <div class="space-y-3 max-w-4xl">
            <h1 class="text-3xl sm:text-5xl font-black leading-tight tracking-tight">
                حقيبة الابتكار والتعبير لليافعين
            </h1>
            <p class="text-base sm:text-lg text-neutral-300 font-medium leading-relaxed">
                مقاربة تربوية ومجتمعية متكاملة تهدف إلى تمكين الفتيات والفتيان من سن 10 إلى 18+ سنة في مختلف ولايات الوطن، من خلال تزويدهم بمساحات آمنة، أدوات إبداعية، ومهارات حياتية تعزز ثقتهم بأنفسهم وتدعم مشاركتهم الإيجابية في مجتمعاتهم المحلية.
            </p>
        </div>

        <!-- أهداف الحقيبة السريعة -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-neutral-800 text-xs">
            <div class="p-3 border border-neutral-700 bg-neutral-900 space-y-1">
                <div class="font-bold text-white uppercase">[ 01 ] مساحات آمنة للتعبير</div>
                <div class="text-neutral-400">توفير بيئات محفزة ومحمية للأطفال واليافعين للتعبير الحر عن مشاعرهم وأفكارهم.</div>
            </div>
            <div class="p-3 border border-neutral-700 bg-neutral-900 space-y-1">
                <div class="font-bold text-white uppercase">[ 02 ] المهارات الحياتية العشر</div>
                <div class="text-neutral-400">التفكير النقدي، التواصل، العمل الجماعي، حل النزاعات، وإدارة المشاعر والضغوط.</div>
            </div>
            <div class="p-3 border border-neutral-700 bg-neutral-900 space-y-1">
                <div class="font-bold text-white uppercase">[ 03 ] المبادرة والقيادة المجتمعية</div>
                <div class="text-neutral-400">تحفيز اليافعين على قيادة مشاريع ومبادرات ابتكارية تخدم بيئتهم ومحيطهم.</div>
            </div>
        </div>
    </section>

    <!-- إحصائيات الأثر والأنشطة العامة (Public Impact Counters) -->
    <section class="space-y-3">
        <div class="border-b-2 border-black pb-2 flex items-center justify-between">
            <h2 class="text-sm font-black text-black uppercase tracking-wider">
                [ 📊 ] المؤشرات الوطنية للنشاط والأثر الميداني
            </h2>
            <span class="text-xs font-mono font-bold text-neutral-600">بيانات التتبع الميداني المعتمدة</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- إجمالي اليافعين المستفيدين -->
            <div class="bg-white border-2 border-black p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-black pb-1">
                    <span>اليافعون واليافعات المستفيدون</span>
                    <span class="font-mono text-[10px]">[ BENEFICIARIES ]</span>
                </div>
                <div class="text-4xl font-black text-black font-mono">
                    {{ number_format($stats['beneficiaries']) }}
                </div>
                <div class="text-xs text-neutral-600">
                    مشاركون مسجلون في الورشات التفاعلية بمختلف المؤسسات
                </div>
            </div>

            <!-- الدورات التدريبية والورشات -->
            <div class="bg-white border-2 border-black p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-black pb-1">
                    <span>الجلسات التدريبية المنفذة والمعتمدة</span>
                    <span class="font-mono text-[10px]">[ SESSIONS ]</span>
                </div>
                <div class="text-4xl font-black text-black font-mono">
                    {{ number_format($stats['sessions']) }}
                </div>
                <div class="text-xs text-neutral-600">
                    جلسات تنشيطية معتمدة وفق المعايير المنهجية للحقيبة
                </div>
            </div>

            <!-- المؤسسات والمراكز الحاضنة -->
            <div class="bg-white border-2 border-black p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-neutral-600 border-b border-black pb-1">
                    <span>المؤسسات والمراكز الشريكة</span>
                    <span class="font-mono text-[10px]">[ INSTITUTIONS ]</span>
                </div>
                <div class="text-4xl font-black text-black font-mono">
                    {{ number_format($stats['institutions']) }}
                </div>
                <div class="text-xs text-neutral-600">
                    دور شباب، مراكز ثقافية، وجمعيات شريكة في التنشيط
                </div>
            </div>
        </div>
    </section>

    <!-- محاور حقيبة الابتكار والتعبير الأربعة -->
    <section class="space-y-4">
        <div class="border-b-2 border-black pb-2">
            <h2 class="text-sm font-black text-black uppercase tracking-wider">
                [ 🧰 ] مجالات النشاط ومحاور التعلم الأربعة
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border-2 border-black p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                <div class="flex items-center justify-between border-b border-black pb-2">
                    <span class="font-black text-sm text-black">1. التعبير الإبداعي والفنون (Arts & Expression)</span>
                    <span class="px-2 py-0.5 bg-black text-white text-[10px] font-mono font-bold">المحور الأول</span>
                </div>
                <p class="text-xs text-neutral-700 leading-relaxed">
                    استخدام وسائط الرسم، النحت، المسرح، الكتابة السردية، وصناعة الدمى لتحفيز الخيال، وتفريغ الشحنات الانفعالية، وبناء الهوية الإيجابية لليافع في مساحة خالية من الأحكام المسبقة.
                </p>
            </div>

            <div class="bg-white border-2 border-black p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                <div class="flex items-center justify-between border-b border-black pb-2">
                    <span class="font-black text-sm text-black">2. المهارات الحياتية وبناء الذات (Life Skills)</span>
                    <span class="px-2 py-0.5 bg-black text-white text-[10px] font-mono font-bold">المحور الثاني</span>
                </div>
                <p class="text-xs text-neutral-700 leading-relaxed">
                    تطوير كفاءات التفكير النقدي، اتخاذ القرار المستنير، الصمود النفسي، الوعي بالذات، ومهارات التواصل الحواري الفعال مع الأقران والأسرة والمجتمع.
                </p>
            </div>

            <div class="bg-white border-2 border-black p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                <div class="flex items-center justify-between border-b border-black pb-2">
                    <span class="font-black text-sm text-black">3. حل النزاعات وبناء التماسك (Conflict Management)</span>
                    <span class="px-2 py-0.5 bg-black text-white text-[10px] font-mono font-bold">المحور الثالث</span>
                </div>
                <p class="text-xs text-neutral-700 leading-relaxed">
                    تعلم أساليب الوساطة الإيجابية، التفاوض البناء، تقبل التنوع والاختلاف، ونبذ العنف بكل أشكاله داخل الفضاءات المدرسية والشبابية والمجتمعية.
                </p>
            </div>

            <div class="bg-white border-2 border-black p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2">
                <div class="flex items-center justify-between border-b border-black pb-2">
                    <span class="font-black text-sm text-black">4. القيادة والمبادرة المجتمعية (Community Engagement)</span>
                    <span class="px-2 py-0.5 bg-black text-white text-[10px] font-mono font-bold">المحور الرابع</span>
                </div>
                <p class="text-xs text-neutral-700 leading-relaxed">
                    تمكين اليافعين من تحديد احتياجات أحيائهم وبلدياتهم، وتصميم مشاريع عمل تطوعية ومبادرات بيئية واجتماعية تعزز روح المواطنة الفاعلة والمسؤولية المشتركة.
                </p>
            </div>
        </div>
    </section>

    <!-- المبادئ التوجيهية العشرة (Guiding Principles) -->
    <section class="bg-white border-2 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-4">
        <div class="border-b-2 border-black pb-2">
            <h2 class="text-sm font-black text-black uppercase tracking-wider">
                [ 📜 ] المبادئ التوجيهية للعمل الميداني مع اليافعين
            </h2>
            <p class="text-xs text-neutral-600 mt-0.5">
                تلتزم كافة الجلسات والورشات التدريبية بالمبادئ العشرة المعتمدة دولياً ووطنياً لضمان بيئة آمنة ودامجة:
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">01. الحماية والأمان</div>
                <div class="text-[11px] text-neutral-600">بيئة خالية تماماً من العنف والإيذاء الجسدي أو المعنوي.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">02. الشمول والإنصاف</div>
                <div class="text-[11px] text-neutral-600">فرص متساوية لكافة اليافعين ودمج ذوي الإعاقة.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">03. المشاركة الحقيقية</div>
                <div class="text-[11px] text-neutral-600">اليافع شريك فاعل في التخطيط وصنع القرار وليس متلقياً سلبياً.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">04. بناء الأمل والتمكين</div>
                <div class="text-[11px] text-neutral-600">التركيز على نقاط القوة وبناء نظرة إيجابية واعدة للمستقبل.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">05. احترام الهوية</div>
                <div class="text-[11px] text-neutral-600">صون الكرامة الإنسانية والاعتزاز بالهوية والانتماء.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">06. التعبير الحر</div>
                <div class="text-[11px] text-neutral-600">تشجيع التعبير عن الآراء والمشاعر بحرية واحترام متبادل.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">07. التعلم بالعمل</div>
                <div class="text-[11px] text-neutral-600">منهجية تفاعلية قائمة على التجربة، اللعب الهادف، والنشاط العملي.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">08. العمل الجماعي</div>
                <div class="text-[11px] text-neutral-600">ترسيخ قيم التعاون والتكافل وروح الفريق الواحد.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">09. التنوع والتماسك</div>
                <div class="text-[11px] text-neutral-600">الاحتفاء بالاختلافات كعنصر إثراء وبناء جسور التعايش.</div>
            </div>
            <div class="border border-black p-3 space-y-1 bg-neutral-50">
                <div class="font-black font-mono">10. الاستدامة والأثر</div>
                <div class="text-[11px] text-neutral-600">ربط المهارات المكتسبة بالسياق المعيشي واستمراريتها على المدى الطويل.</div>
            </div>
        </div>
    </section>

    <!-- الفئات العمرية المشمولة بالبرنامج -->
    <section class="bg-black text-white p-6 border-2 border-black space-y-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <div class="border-b border-neutral-700 pb-2">
            <h2 class="text-sm font-black text-white uppercase tracking-wider">
                [ 🎯 ] الفئات الديموغرافية والعمرية المستهدفة (من سن 10 إلى 18+ سنة)
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-mono">
            <div class="border border-neutral-700 p-3 bg-neutral-900 space-y-1">
                <div class="text-white font-bold font-sans">المرحلة الأولى: اليافعون الصغار [ 10 - 13 سنة ]</div>
                <div class="text-neutral-400 font-sans text-[11px]">بناء الثقة، التعرف على المشاعر، وتطوير المهارات التعبيرية الأولية عبر الفنون والألعاب الحركية.</div>
            </div>
            <div class="border border-neutral-700 p-3 bg-neutral-900 space-y-1">
                <div class="text-white font-bold font-sans">المرحلة الثانية: اليافعون المتوسطون [ 13 - 15 سنة ]</div>
                <div class="text-neutral-400 font-sans text-[11px]">التفكير النقدي، مهارات التواصل الحواري، مقاومة الضغوط السلبية، وبناء الهوية الشخصية.</div>
            </div>
            <div class="border border-neutral-700 p-3 bg-neutral-900 space-y-1">
                <div class="text-white font-bold font-sans">المرحلة الثالثة: اليافعون الكبار [ 15 - 18+ سنة ]</div>
                <div class="text-neutral-400 font-sans text-[11px]">القيادة المجتمعية، إدارة النزاعات، التخطيط للمستقبل، وتصميم المبادرات المحلية النافعة.</div>
            </div>
        </div>
    </section>

</div>
@endsection
