<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Copie les anciens numéros uniquement si les nouvelles colonnes sont vides
        DB::statement('
            UPDATE sections 
            SET 
                tel_pf1 = COALESCE(NULLIF(tel_pf1, ""), `TEL-PF1`),
                tel_pf2 = COALESCE(NULLIF(tel_pf2, ""), `TEL-PF2`),
                tel_pf3 = COALESCE(NULLIF(tel_pf3, ""), `TEL-PF3`)
        ');

        // Supprime les anciennes colonnes devenues inutiles
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['TEL-PF1', 'TEL-PF2', 'TEL-PF3']);
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('TEL-PF1')->nullable();
            $table->string('TEL-PF2')->nullable();
            $table->string('TEL-PF3')->nullable();
        });
    }
};
