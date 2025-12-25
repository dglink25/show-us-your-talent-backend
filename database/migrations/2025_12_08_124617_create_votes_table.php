<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('email_payeur');
            $table->decimal('montant', 10, 2);
            $table->string('devise')->default('XOF');
            $table->enum('methode', ['mobile_money', 'carte_bancaire', 'virement', 'wave', 'orange_money', 'mtn_money']);
            $table->enum('statut', ['en_attente', 'paye', 'echec', 'annule', 'rembourse'])->default('en_attente');
            $table->json('metadata')->nullable();
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamp('paye_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('payments');
    }
};