<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifie si les anciennes colonnes existent avant de copier et supprimer
        if (Schema::hasColumns('sections', ['TEL-PF1', 'TEL-PF2', 'TEL-PF3'])) {
            DB::statement('
                UPDATE sections 
                SET 
                    tel_pf1 = COALESCE(NULLIF(tel_pf1, ""), `TEL-PF1`),
                    tel_pf2 = COALESCE(NULLIF(tel_pf2, ""), `TEL-PF2`),
                    tel_pf3 = COALESCE(NULLIF(tel_pf3, ""), `TEL-PF3`)
            ');

            Schema::table('sections', function (Blueprint $table) {
                $table->dropColumn(['TEL-PF1', 'TEL-PF2', 'TEL-PF3']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'TEL-PF1')) {
                $table->string('TEL-PF1')->nullable();
            }
            if (!Schema::hasColumn('sections', 'TEL-PF2')) {
                $table->string('TEL-PF2')->nullable();
            }
            if (!Schema::hasColumn('sections', 'TEL-PF3')) {
                $table->string('TEL-PF3')->nullable();
            }
        });
    }
};
