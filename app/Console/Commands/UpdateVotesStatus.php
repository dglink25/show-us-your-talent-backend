<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Edition;
use Carbon\Carbon;

class UpdateVotesStatus extends Command
{
    protected $signature = 'votes:update-status';
    protected $description = 'Met à jour automatiquement le statut des votes des éditions';

    public function handle()
    {
        $this->info('Mise à jour du statut des votes...');
        
        $editions = Edition::where('statut', 'active')->get();
        $updated = 0;
        
        foreach ($editions as $edition) {
            $oldStatus = $edition->statut_votes;
            $oldVotesOpen = $edition->votes_ouverts;
            
            $edition->mettreAJourStatutVotes();
            
            if ($edition->isDirty(['statut_votes', 'votes_ouverts'])) {
                $edition->saveQuietly();
                $updated++;
                
                $this->info("Édition {$edition->nom} ({$edition->id}) mise à jour :");
                $this->info("  Statut votes: {$oldStatus} -> {$edition->statut_votes}");
                $this->info("  Votes ouverts: " . ($oldVotesOpen ? 'oui' : 'non') . " -> " . ($edition->votes_ouverts ? 'oui' : 'non'));
            }
        }
        
        $this->info("{$updated} édition(s) mise(s) à jour.");
        
        // Programmer cette commande dans le Kernel pour qu'elle s'exécute toutes les minutes
        // App\Console\Kernel.php:
        // $schedule->command('votes:update-status')->everyMinute();
        
        return 0;
    }
}