<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Vérification stricte et indépendante colonne par colonne
            if (!Schema::hasColumn('sections', 'sub_details')) {
                $table->text('sub_details')->nullable();
            }
            if (!Schema::hasColumn('sections', 'pf1')) {
                $table->string('pf1')->nullable();
            }
            if (!Schema::hasColumn('sections', 'tel_pf1')) {
                $table->string('tel_pf1')->nullable();
            }
            if (!Schema::hasColumn('sections', 'pf2')) {
                $table->string('pf2')->nullable();
            }
            if (!Schema::hasColumn('sections', 'tel_pf2')) {
                $table->string('tel_pf2')->nullable();
            }
            if (!Schema::hasColumn('sections', 'pf3')) {
                $table->string('pf3')->nullable();
            }
            if (!Schema::hasColumn('sections', 'tel_pf3')) {
                $table->string('tel_pf3')->nullable();
            }
            if (!Schema::hasColumn('sections', 'status')) {
                $table->string('status')->default('Actif');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['sub_details', 'pf1', 'tel_pf1', 'pf2', 'tel_pf2', 'pf3', 'tel_pf3', 'status']);
        });
    }
};
