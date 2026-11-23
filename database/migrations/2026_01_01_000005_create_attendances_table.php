<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained('training_sessions')->onDelete('cascade');
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            $table->unsignedInteger('serial_number')->default(1); // الرقم التسلسلي في القائمة (01 .. 70)
            $table->boolean('attended')->default(true); // تأكيد الحضور
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['training_session_id', 'beneficiary_id']);
            $table->index(['training_session_id', 'serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
