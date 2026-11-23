<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kit_catalogs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['activity', 'competency']); // نشاط أو مهارة/كفاية
            $table->string('code')->nullable();
            $table->string('title_ar'); // العنوان بالعربية
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_catalogs');
    }
};
