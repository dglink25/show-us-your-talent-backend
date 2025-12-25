// database/migrations/2024_01_02_create_chat_participants_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['promoteur', 'candidat', 'admin'])->default('candidat');
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_muted')->default(false);
            $table->timestamps();
            
            $table->unique(['chat_room_id', 'user_id']);
            $table->index(['user_id', 'last_seen_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_participants');
    }
};