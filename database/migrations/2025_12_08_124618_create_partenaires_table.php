<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['principal', 'officiel', 'media', 'technique'])->default('officiel');
            $table->foreignId('edition_id')->constrained('editions')->onDelete('cascade');
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partenaires');
    }
};