<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('edition_id')->constrained('editions')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('video_url')->nullable();
            $table->text('description_talent')->nullable();
            $table->enum('statut', ['en_attente', 'validee', 'refusee', 'preselectionne', 'elimine', 'finaliste', 'gagnant'])->default('en_attente');
            $table->integer('phase_actuelle')->default(1); // 1=Présélection, 2=Phase 1, 3=Phase 2, 4=Finale
            $table->integer('note_jury')->nullable();
            $table->integer('nombre_votes')->default(0);
            $table->text('motif_refus')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users');
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
            
            // Un candidat ne peut postuler qu'une fois par catégorie par édition
            $table->unique(['candidat_id', 'edition_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};