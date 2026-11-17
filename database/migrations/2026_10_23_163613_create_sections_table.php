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
        Schema::create('sections', function (Blueprint $table) {

            $table->id()->autoIncrement()->unique()->nullable();
            $table->string('Nom')->nullable();
            $table->foreignId('conseil_id')->constrained()->cascadeOnDelete();
            // $table->string('Conseil')->nullable();
            $table->string('PF1')->nullable();
            $table->string('TEL_PF1')->nullable();
            $table->string('PF2')->nullable();
            $table->string('TEL_PF2')->nullable();
            $table->string('PF3')->nullable();
            $table->string('TEL_PF3')->nullable();
            $table->string('status', 30)->default('Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
