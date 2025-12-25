<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('edition_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['category_id', 'edition_id']);
            $table->unique(['category_id', 'edition_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};