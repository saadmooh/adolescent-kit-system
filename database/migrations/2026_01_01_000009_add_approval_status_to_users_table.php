<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('approval_status')->default('pending')->after('role'); // pending, approved, rejected
            $table->boolean('is_active')->default(false)->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('is_active');
            $table->text('admin_notes')->nullable()->after('approved_at');
        });

        // اعتماد وتفعيل الحسابات الحالية تلقائياً (مدير النظام والميسرين الحاليين)
        DB::table('users')->update([
            'approval_status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'is_active', 'approved_at', 'admin_notes']);
        });
    }
};
