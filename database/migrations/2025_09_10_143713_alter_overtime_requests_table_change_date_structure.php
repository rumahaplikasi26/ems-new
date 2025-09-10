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
        Schema::table('overtime_requests', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['date', 'start_time', 'end_time']);
            
            // Add new timestamp columns
            $table->timestamp('start_date')->after('reason');
            $table->timestamp('end_date')->after('start_date');
            
            // Update indexes
            $table->dropIndex(['employee_id', 'date']);
            $table->index(['employee_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['start_date', 'end_date']);
            
            // Add back old columns
            $table->date('date')->after('reason');
            $table->time('start_time')->after('date');
            $table->time('end_time')->after('start_time');
            
            // Restore old indexes
            $table->dropIndex(['employee_id', 'start_date']);
            $table->index(['employee_id', 'date']);
        });
    }
};