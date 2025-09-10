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
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->text('reason');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('priority', ['minor', 'major'])->default('minor');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index(['employee_id', 'date']);
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_requests');
    }
};