<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Ajouter les colonnes si elles n'existent pas
            if (!Schema::hasColumn('users', 'nom')) {
                $table->string('nom')->nullable();
            }

            if (!Schema::hasColumn('users', 'prenoms')) {
                $table->string('prenoms')->nullable();
            }

            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone')->nullable();
            }

            if (!Schema::hasColumn('users', 'date_naissance')) {
                $table->date('date_naissance')->nullable();
            }

            if (!Schema::hasColumn('users', 'sexe')) {
                $table->enum('sexe', ['M', 'F', 'Autre'])->nullable();
            }

            if (!Schema::hasColumn('users', 'photo_url')) {
                $table->string('photo_url')->nullable();
            }

            if (!Schema::hasColumn('users', 'origine')) {
                $table->string('origine')->nullable();
            }

            if (!Schema::hasColumn('users', 'ethnie')) {
                $table->string('ethnie')->nullable();
            }

            if (!Schema::hasColumn('users', 'universite')) {
                $table->string('universite')->nullable();
            }

            if (!Schema::hasColumn('users', 'filiere')) {
                $table->string('filiere')->nullable();
            }

            if (!Schema::hasColumn('users', 'annee_etude')) {
                $table->string('annee_etude')->nullable();
            }

            if (!Schema::hasColumn('users', 'type_compte')) {
                $table->enum('type_compte', ['candidat', 'promoteur', 'admin'])
                      ->default('candidat');
            }

            if (!Schema::hasColumn('users', 'compte_actif')) {
                $table->boolean('compte_actif')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nom',
                'prenoms',
                'telephone',
                'date_naissance',
                'sexe',
                'photo_url',
                'origine',
                'ethnie',
                'universite',
                'filiere',
                'annee_etude',
                'type_compte',
                'compte_actif',
            ]);
        });
    }
};
