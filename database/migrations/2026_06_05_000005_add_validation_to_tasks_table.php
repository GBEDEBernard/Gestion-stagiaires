<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-005 — Validation/clôture pilotée par l'ADMIN.
     * À 100 %, la tâche passe « en attente de validation » ; seul l'admin la
     * clôture (status=completed) ou la rouvre. On trace qui/quand valide.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('validated_by')->nullable()->after('completed_at')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable()->after('validated_by');
            // Marqueur explicite « discussion rouverte » par un admin après clôture.
            $table->timestamp('discussion_reopened_at')->nullable()->after('validated_at');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validated_by', 'validated_at', 'discussion_reopened_at']);
        });
    }
};
