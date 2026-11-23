<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('تدريب حقيبة الابتكار والتعبير لليافعين Kit Adolescent');
            $table->date('session_date'); // يوم ---/---/2026
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->string('venue')->nullable(); // المكان
            $table->string('wilaya')->nullable(); // الولاية
            $table->string('municipality')->nullable(); // البلدية
            
            // الميسرين الأساسيين
            $table->foreignId('lead_facilitator_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('lead_facilitator_2_id')->nullable()->constrained('users')->nullOnDelete();
            
            // المساعدون (حتى 6 ميسرين مساعدين كالموجود في الوثيقة)
            $table->json('assistant_facilitators')->nullable(); // قائمة بأسماء أو معرفات المساعدين الستة
            
            // الانتقال
            $table->string('transit_from_municipality')->nullable(); // من بلدية...
            $table->string('transit_to_municipality')->nullable(); // إلى بلدية...
            
            // مصادقة المؤسسة / الختم والتوقيع
            $table->boolean('institution_approved')->default(false);
            $table->string('institution_signature_path')->nullable();
            $table->timestamp('institution_approved_at')->nullable();
            
            $table->enum('status', ['draft', 'ongoing', 'completed', 'approved'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
