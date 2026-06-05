<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-005 — Discussion « vraie messagerie » (WhatsApp-like).
     * On enrichit task_messages : réponse citée (parent_id) + pièce jointe
     * (vocal / image / fichier).
     */
    public function up(): void
    {
        Schema::table('task_messages', function (Blueprint $table) {
            // Réponse citée (façon WhatsApp) : pointe vers le message/rapport cité.
            $table->foreignId('parent_id')
                ->nullable()
                ->after('user_id')
                ->constrained('task_messages')
                ->nullOnDelete();

            // Pièce jointe (vocal / image / fichier).
            $table->string('attachment_type')->nullable()->after('body');   // audio | image | file
            $table->string('attachment_path')->nullable()->after('attachment_type');
            $table->string('attachment_name')->nullable()->after('attachment_path'); // nom d'origine
            $table->string('attachment_mime')->nullable()->after('attachment_name');
            $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime'); // octets
            $table->unsignedInteger('attachment_duration')->nullable()->after('attachment_size'); // secondes (audio)

            // Édition / suppression douce (fonctions « vraie messagerie »).
            $table->timestamp('edited_at')->nullable()->after('attachment_duration');

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('task_messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'attachment_type',
                'attachment_path',
                'attachment_name',
                'attachment_mime',
                'attachment_size',
                'attachment_duration',
                'edited_at',
            ]);
        });
    }
};
