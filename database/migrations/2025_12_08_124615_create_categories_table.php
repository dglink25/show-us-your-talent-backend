<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('annee');
            $table->integer('numero_edition');
            $table->text('description')->nullable();
            $table->enum('statut', ['brouillon', 'active', 'terminee', 'archivee'])->default('brouillon');
            $table->boolean('inscriptions_ouvertes')->default(false);
            $table->dateTime('date_debut_inscriptions')->nullable();
            $table->dateTime('date_fin_inscriptions')->nullable();
            $table->boolean('votes_ouverts')->default(false);
            $table->dateTime('date_debut_votes')->nullable();
            $table->dateTime('date_fin_votes')->nullable();
            $table->foreignId('promoteur_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};