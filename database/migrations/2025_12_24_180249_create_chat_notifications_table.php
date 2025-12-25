// database/migrations/2024_01_04_create_chat_notifications_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('chat_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('chat_message_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index(['chat_room_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_notifications');
    }
};