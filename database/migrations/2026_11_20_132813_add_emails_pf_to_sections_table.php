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
        Schema::table('sections', function (Blueprint $table) {
            $table->string('EMAIL_PF1')->nullable()->after('TEL_PF1');
            $table->string('EMAIL_PF2')->nullable()->after('TEL_PF2');
            $table->string('EMAIL_PF3')->nullable()->after('TEL_PF3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['EMAIL_PF1', 'EMAIL_PF2', 'EMAIL_PF3']);
        });
    }
};
