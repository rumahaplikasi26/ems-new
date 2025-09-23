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
        Schema::table('financial_requests', function (Blueprint $table) {
            $table->text('file_path')->nullable()->after('receipt_image_url');
            $table->text('file_url')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_requests', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_url']);
        });
    }
};
