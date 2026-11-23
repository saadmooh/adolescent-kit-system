<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_report_id')->constrained('activity_reports')->onDelete('cascade');
            $table->enum('category', [
                'institution_gate',     // الباب الخارجي للمؤسسة التي أجري بها النشاط
                'facilitators_team',    // طاقم الميسرين
                'group_photo',          // صورة جماعية لليافعين والهيئة المستقبلة والميسرين
                'in_action',            // صورة لجميع اليافعين أثناء النشاط
                'activity_video',       // فيديو للنشاط
                'extra'                 // صور ومرفقات إضافية
            ]);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0); // بالبايت
            $table->timestamps();

            $table->index(['activity_report_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_attachments');
    }
};
