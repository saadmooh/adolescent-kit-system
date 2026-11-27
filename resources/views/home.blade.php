@extends('layouts.app')

@section('title', 'حقيبة الابتكار والتعبير لليافعين')

@section('content')
<div class="-mx-4 sm:-mx-6 -my-6">

    <!-- الواجهة الرئيسية: صورة Hero مع نصوص قليلة -->
    <section class="relative overflow-hidden border-y-2 border-unicef-dark shadow-[0px_8px_0px_0px_rgba(0,47,108,1)]">
        <img src="{{ asset('images/hero-youth.jpg') }}"
             alt="لقطة واقعية معبّرة ليافعين ويافعات يضحكون ويرسمون معاً داخل فضاء شبابي مشرق"
             class="w-full h-[62vh] sm:h-[74vh] object-cover">

        <!-- طبقة تعتيم لتحسين وضوح النص -->
        <div class="absolute inset-0 bg-gradient-to-t from-unicef-dark/90 via-unicef-dark/55 to-unicef-dark/30"></div>

        <!-- النصوص القليلة -->
        <div class="absolute inset-0 flex items-center justify-center p-6">
            <div class="max-w-3xl text-center text-white space-y-4">
                <span class="inline-block px-3 py-1 bg-unicef-blue text-white text-[11px] font-bold uppercase tracking-wider border-2 border-white">
                    قطاع الشباب والرياضة &times; اليونيسف &bull; منظومة M&amp;E
                </span>
                <h1 class="text-3xl sm:text-5xl font-black leading-tight tracking-tight">
                    حقيبة الابتكار والتعبير لليافعين
                </h1>
                <p class="text-sm sm:text-lg text-neutral-200 font-medium leading-relaxed">
                    مساحات آمنة وأدوات إبداعية لتمكين الفتيات والفتيان من سن 10 إلى 18+ سنة.
                </p>
            </div>
        </div>
    </section>

</div>
@endsection
