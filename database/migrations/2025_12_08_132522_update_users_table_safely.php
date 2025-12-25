<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Désactiver temporairement les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // 2. Sauvegarder les données existantes si nécessaire
        // (cette partie dépend de vos besoins)
        
        // 3. Supprimer l'ancienne table users
        Schema::dropIfExists('users');
        
        // 4. Recréer la table users avec les nouvelles colonnes
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenoms');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable pour candidats
            $table->string('telephone')->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('sexe', ['M', 'F', 'Autre'])->nullable();
            $table->string('photo_url')->nullable();
            $table->string('origine')->nullable();
            $table->string('ethnie')->nullable();
            $table->string('universite')->nullable();
            $table->string('filiere')->nullable();
            $table->string('annee_etude')->nullable();
            $table->enum('type_compte', ['candidat', 'promoteur', 'admin'])->default('candidat');
            $table->boolean('compte_actif')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // 5. Réactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void{
        // Pour rollback, vous devrez peut-être recréer l'ancienne structure
        // Cette partie dépend de votre structure précédente
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('users');
        
        // Recréer l'ancienne table si nécessaire
        // Schema::create('users', function (Blueprint $table) {
        //     // Ancienne structure
        // });
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};