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
            // Vérifiez si c'est 'tel-pf1' en minuscules dans votre première migration
            if (Schema::hasColumn('sections', 'tel-pf1')) {
                $table->renameColumn('tel-pf1', 'TEL_PF1');
                $table->renameColumn('tel-pf2', 'TEL_PF2');
                $table->renameColumn('tel-pf3', 'TEL_PF3');
            } elseif (Schema::hasColumn('sections', 'TEL-PF1')) {
                $table->renameColumn('TEL-PF1', 'TEL_PF1');
                $table->renameColumn('TEL-PF2', 'TEL_PF2');
                $table->renameColumn('TEL-PF3', 'TEL_PF3');
            }
        });
    }

    public function down(): void
    {
        Schema::table(
            'sections',
            function (Blueprint $table) {
                if (Schema::hasColumn('sections', 'TEL_PF1')) {
                    $table->renameColumn('TEL_PF1', 'tel-pf1');
                    $table->renameColumn('TEL_PF2', 'tel-pf2');
                    $table->renameColumn('TEL_PF3', 'tel-pf3');
                }
            }
        );
    }
};
