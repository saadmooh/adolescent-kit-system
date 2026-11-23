<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->unique()->constrained('training_sessions')->onDelete('cascade');
            
            // الأنشطة والكفايات المحققة (قوائم المعرفات أو العناوين المختارة)
            $table->json('selected_activities')->nullable(); // الأنشطة المقدمة
            $table->json('selected_competencies')->nullable(); // المهارات / الكفايات المحققة

            // معلومات سير النشاط (التقييم النوعي)
            $table->text('facilities')->nullable(); // التسهيلات
            $table->text('difficulties')->nullable(); // الصعوبات
            $table->text('strengths')->nullable(); // نقاط القوة
            $table->text('weaknesses')->nullable(); // نقاط الضعف
            $table->text('engagement_notes')->nullable(); // تفاعل اليافعين
            $table->text('attendance_notes')->nullable(); // الحضور
            $table->text('general_remarks')->nullable(); // ملاحظات أخرى
            $table->text('recommendations')->nullable(); // توصيات

            // التوقيعات والاعتماد
            $table->foreignId('rapporteur_id')->nullable()->constrained('users')->nullOnDelete(); // المقرر
            $table->string('rapporteur_wilaya')->nullable(); // الولاية... في: .../.../......
            $table->date('rapporteur_date')->nullable();
            $table->string('rapporteur_signature_path')->nullable(); // صورة توقيع المقرر

            // بطاقة تلخيصية ومصادقة الهيئة المستقبلة
            $table->string('host_contact_name')->nullable();
            $table->string('host_contact_title')->nullable();
            $table->string('host_contact_email')->nullable();
            $table->string('host_contact_phone')->nullable();
            $table->string('host_signature_path')->nullable();
            $table->date('host_approval_date')->nullable();
            $table->string('host_wilaya')->nullable();

            // حفظ نسخة إحصائية مجمدة (Frozen Analytics Snapshot)
            $table->unsignedInteger('cached_total_attendees')->default(0);
            $table->unsignedInteger('cached_male_count')->default(0);
            $table->unsignedInteger('cached_female_count')->default(0);
            $table->unsignedInteger('cached_schooled_count')->default(0);
            $table->unsignedInteger('cached_unschooled_count')->default(0);
            $table->json('cached_age_distribution')->nullable();
            $table->json('cached_education_breakdown')->nullable();
            $table->unsignedInteger('cached_disabled_count')->default(0);
            $table->json('cached_disabilities_breakdown')->nullable();
            $table->unsignedInteger('cached_chronic_diseases_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_reports');
    }
};
