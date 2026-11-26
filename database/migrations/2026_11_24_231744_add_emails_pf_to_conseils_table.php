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
                $table->string('email_pf1')->nullable()->after('tel_pf1');
                $table->string('email_pf2')->nullable()->after('tel_pf2');
                $table->string('email_pf3')->nullable()->after('tel_pf3');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conseils', function (Blueprint $table) {
            Schema::table('conseils', function (Blueprint $table) {
                $table->dropColumn(['email_pf1', 'email_pf2', 'email_pf3']);
            });
        });
    }
};
