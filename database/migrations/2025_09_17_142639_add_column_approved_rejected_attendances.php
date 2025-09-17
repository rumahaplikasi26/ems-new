<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->nullOnUpdate();
            $table->datetime('approved_at')->nullable();
        });

        Schema::table('attendance_temps', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->nullOnUpdate();
            $table->datetime('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
        });

        Schema::table('attendance_temps', function (Blueprint $table) {
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
