<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('editions', function (Blueprint $table) {
            $table->enum('statut_votes', ['en_attente', 'en_cours', 'termine', 'suspendu'])->default('en_attente')->after('date_fin_votes');
            
            // Index pour optimisation
            $table->index('date_debut_votes');
            $table->index('date_fin_votes');
            $table->index('statut_votes');
        });
    }

    public function down(){
        Schema::table('editions', function (Blueprint $table) {
            $table->dropColumn(['date_debut_votes', 'date_fin_votes', 'statut_votes']);
            $table->dropIndex(['date_debut_votes', 'date_fin_votes', 'statut_votes']);
        });
    }
};