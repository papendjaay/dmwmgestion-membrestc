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
            // $table->dropUnique(['id']);
            // $table->dropUnique(['Nom']);
            // $table->dropUnique(['Conseil']);
            // $table->dropUnique(['PF1']);
            // $table->dropUnique(['TEL-PF1']);
            // $table->dropUnique(['PF2']);
            // $table->dropUnique(['TEL-PF2']);
            // $table->dropUnique(['PF3']);
            // $table->dropUnique(['TEL-PF3']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            //
        });
    }
};
