<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(){
        $editions = \App\Models\Edition::all();
        
        foreach ($editions as $edition) {
            // Créer une instance pour utiliser la méthode
            $editionModel = new \App\Models\Edition();
            $editionModel->fill($edition->toArray());
            $editionModel->mettreAJourStatutVotes();
            
            // Mettre à jour si nécessaire
            if ($edition->statut_votes !== $editionModel->statut_votes || 
                $edition->votes_ouverts !== $editionModel->votes_ouverts) {
                
                \App\Models\Edition::where('id', $edition->id)->update([
                    'statut_votes' => $editionModel->statut_votes,
                    'votes_ouverts' => $editionModel->votes_ouverts,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
