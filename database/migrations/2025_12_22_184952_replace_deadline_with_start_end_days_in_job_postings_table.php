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
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('deadline');
            $table->date('start_day')->nullable()->after('category');
            $table->date('end_day')->nullable()->after('start_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn(['start_day', 'end_day']);
            $table->date('deadline')->nullable()->after('category');
        });
    }
};
