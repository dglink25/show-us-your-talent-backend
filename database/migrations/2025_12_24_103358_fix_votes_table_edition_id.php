<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Vérifier si la colonne edition_id existe
        if (!Schema::hasColumn('votes', 'edition_id')) {
            Schema::table('votes', function (Blueprint $table) {
                // Ajouter la colonne edition_id
                $table->foreignId('edition_id')
                    ->after('id')
                    ->nullable()
                    ->constrained('editions')
                    ->onDelete('cascade');
            });
        }
        
        // Vérifier aussi candidat_id et user_id (votant_id)
        if (!Schema::hasColumn('votes', 'candidat_id')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->foreignId('candidat_id')
                    ->after('edition_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('cascade');
            });
        }
        
        if (!Schema::hasColumn('votes', 'user_id')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->after('candidat_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('cascade');
            });
        }
        
        // Si votant_id existe, le renommer en user_id
        if (Schema::hasColumn('votes', 'votant_id') && !Schema::hasColumn('votes', 'user_id')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->renameColumn('votant_id', 'user_id');
            });
        }
    }

    public function down()
    {
        // Ne pas supprimer en rollback pour préserver les données
    }
};