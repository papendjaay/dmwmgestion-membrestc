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
        Schema::create('conseils', function (Blueprint $table) {
            $table->id();

            $table->string('Nom');
            $table->string('Code', 10)->unique();
            $table->string('PF1');
            $table->string('tel_pf1', 30)->nullable();
            $table->string('PF2');
            $table->string('tel_pf2', 30)->nullable();
            $table->string('PF3');
            $table->string('tel_pf3', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conseils');
    }
};
