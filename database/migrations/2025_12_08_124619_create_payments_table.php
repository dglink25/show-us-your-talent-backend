<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->onDelete('cascade');
            $table->foreignId('votant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('email_votant')->nullable(); // Pour les votes sans compte
            $table->string('ip_address')->nullable();
            $table->decimal('montant', 10, 2)->default(0); // Prix du vote
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->timestamps();
            
            // Limiter les votes par email/IP (à adapter selon besoins)
            $table->unique(['candidature_id', 'email_votant']);
            $table->unique(['candidature_id', 'votant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};