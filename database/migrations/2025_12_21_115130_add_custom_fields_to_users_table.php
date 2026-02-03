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
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_card_number')->unique()->after('email');
            $table->text('address')->after('identity_card_number');
            $table->string('university')->after('address');
            $table->string('last_3_numbers', 3)->after('university');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['identity_card_number', 'address', 'university', 'last_3_numbers']);
        });
    }
};
