<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edition_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions')->onDelete('cascade');
            $table->string('nom');
            $table->integer('numero_phase'); // 1 à 4
            $table->text('description')->nullable();
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edition_phases');
    }
};