<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المؤسسة
            $table->string('wilaya')->nullable(); // الولاية
            $table->string('municipality')->nullable(); // البلدية
            $table->string('address')->nullable(); // المكان / العنوان
            $table->string('category')->default('متمدرسين'); // متمدرسين، معاقين، كشافة، مراكز شباب...
            $table->string('contact_person_name')->nullable(); // جهة الاتصال: الاسم واللقب
            $table->string('contact_person_title')->nullable(); // الصفة
            $table->string('contact_person_email')->nullable(); // البريد الإلكتروني
            $table->string('contact_person_phone')->nullable(); // رقم الهاتف
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
