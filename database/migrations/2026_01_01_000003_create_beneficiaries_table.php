<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // الاسم واللقب
            $table->date('birth_date'); // تاريخ الميلاد
            $table->enum('gender', ['ذكر', 'أنثى']); // الجنس: ذكر / أنثى
            $table->string('municipality'); // البلدية
            $table->string('education_level')->default('متوسط'); // ابتدائي، متوسط، ثانوي، تكوين مهني، غير متمدرس
            $table->boolean('is_schooled')->default(true); // متمدرس: نعم / لا
            
            // الحالة الصحية والأمراض المزمنة
            $table->boolean('has_chronic_disease')->default(false); // المرض المزمن
            $table->string('chronic_disease_details')->nullable(); // نوع المرض المزمن
            
            // فئة خاصة والإعاقة
            $table->boolean('has_disability')->default(false); // معاق: نعم / لا
            $table->string('disability_type')->nullable(); // حركية، بصرية، الصم البكم، شيء آخر
            $table->string('disability_details')->nullable(); // اذكرها
            $table->string('second_disability')->nullable(); // إعاقة ثانية
            $table->string('other_health_issues')->nullable(); // مشاكل صحية أخرى
            $table->text('psychosocial_notes')->nullable(); // ملاحظات حول الجانب النفسي/الاجتماعي
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
