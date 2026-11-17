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
        Schema::create('enrolements', function (Blueprint $table) {
            $table->id();
            $table->string('internal_id')->unique()->nullable();
            $table->string('Prenom')->nullable();
            $table->string('Nom')->nullable();
            $table->string('Genre', 20)->nullable();
            $table->integer('Age')->nullable();
            $table->string('Niveau_Etude')->nullable();
            $table->string('Profession')->nullable();
            $table->string('Conseil')->nullable();
            $table->string('Section')->nullable();
            $table->string('Telephone', 30)->nullable();
            $table->string('Adresse')->nullable();
            $table->string('Type_piece_identite')->nullable();
            $table->string('Numero_piece_identite')->nullable();
            $table->string('Parent')->nullable();
            $table->string('Lien_parental')->nullable();
            $table->string('Contact', 30)->nullable();
            $table->string('Cree_par')->nullable();
            $table->boolean('Statut')->default(true);
            // ->nullable()
            // ->constrained('users')
            // ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolements');
    }
};
