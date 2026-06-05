<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-005 — Réactions emoji sur les messages/rapports du fil.
     * Un utilisateur = une réaction d'un emoji donné par message.
     */
    public function up(): void
    {
        Schema::create('task_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_message_id')->constrained('task_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('emoji', 16);
            $table->timestamps();

            $table->unique(['task_message_id', 'user_id', 'emoji']);
            $table->index('task_message_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_message_reactions');
    }
};
