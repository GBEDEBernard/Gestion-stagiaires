<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-008 — Mémorise la progression % d'une tâche AU MOMENT où elle est
     * assignée à une équipe (table pivot). Cette « base » reste figée et
     * entre dans le calcul du pourcentage global :
     *   global = (base + progression de chaque membre) / (n + 1)
     * Un participant sans rapport compte pour 0.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('base_progress_percent')
                ->nullable()
                ->default(null)
                ->after('last_progress_percent');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('base_progress_percent');
        });
    }
};