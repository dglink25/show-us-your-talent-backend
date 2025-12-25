<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * 1. Créer la table votes si elle n'existe pas
         */
        if (!Schema::hasTable('votes')) {
            Schema::create('votes', function (Blueprint $table) {
                $table->id();

                $table->foreignId('edition_id')
                    ->constrained('editions')
                    ->cascadeOnDelete();

                $table->foreignId('candidat_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->foreignId('votant_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->foreignId('categorie_id')
                    ->nullable()
                    ->constrained('categories')
                    ->cascadeOnDelete();

                $table->foreignId('candidature_id')
                    ->nullable()
                    ->constrained('candidatures')
                    ->cascadeOnDelete();

                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Index
                $table->index(['edition_id', 'candidat_id']);
                $table->index(['votant_id', 'edition_id']);
            });

            return;
        }

        /**
         * 2. La table existe → mise à jour progressive
         */

        // edition_id
        if (!Schema::hasColumn('votes', 'edition_id')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->foreignId('edition_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('editions')
                    ->cascadeOnDelete();
            });
        }

        // Renommage user_id → votant_id (sans Doctrine)
        if (Schema::hasColumn('votes', 'user_id') && !Schema::hasColumn('votes', 'votant_id')) {
            DB::statement(
                'ALTER TABLE votes CHANGE user_id votant_id BIGINT UNSIGNED'
            );
        }

        // votant_id
        if (!Schema::hasColumn('votes', 'votant_id')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->foreignId('votant_id')
                    ->nullable()
                    ->after('candidat_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            });
        }

        // candidature_id
        if (!Schema::hasColumn('votes', 'candidature_id')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->foreignId('candidature_id')
                    ->nullable()
                    ->after('candidat_id')
                    ->constrained('candidatures')
                    ->cascadeOnDelete();
            });
        }

        // categorie_id
        if (!Schema::hasColumn('votes', 'categorie_id')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->foreignId('categorie_id')
                    ->nullable()
                    ->after('votant_id')
                    ->constrained('categories')
                    ->cascadeOnDelete();
            });
        }

        // Soft deletes
        if (!Schema::hasColumn('votes', 'deleted_at')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        /**
         * 3. Remplir edition_id manquant
         */
        if (Schema::hasColumn('votes', 'edition_id')) {
            $activeEdition = DB::table('editions')
                ->where('statut', 'active')
                ->latest('id')
                ->first();

            if ($activeEdition) {
                DB::table('votes')
                    ->whereNull('edition_id')
                    ->update(['edition_id' => $activeEdition->id]);
            }
        }
    }

    public function down(): void
    {
        // Pas de rollback pour préserver les données existantes
    }
};
