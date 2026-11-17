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
        Schema::table('conseils', function (Blueprint $table) {
            Schema::table('conseils', function (Blueprint $table) {
                if (!Schema::hasColumn('conseils', 'tel_pf1')) {
                    $table->string('tel_pf1')->nullable()->after('pf1');
                }
                if (!Schema::hasColumn('conseils', 'tel_pf2')) {
                    $table->string('tel_pf2')->nullable()->after('pf2');
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conseils', function (Blueprint $table) {
            $table->dropColumn(['tel_pf1', 'tel_pf2']);
        });
    }
};
