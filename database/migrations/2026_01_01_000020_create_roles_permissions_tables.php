<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الأدوار الديناميكي
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();       // مفتاح منطقي ثابت: admin, facilitator, assistant...
            $table->string('name');                 // الاسم المعروض بالعربية
            $table->boolean('is_builtin')->default(false); // الأدوار المدمجة لا تُحذف
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // جدول الصلاحيات
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();        // مثل: sessions.create
            $table->string('label');                // وصف عربي
            $table->string('group');                // مجموعة العرض
            $table->timestamps();
        });

        // جدول الربط بين الأدوار والصلاحيات
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
