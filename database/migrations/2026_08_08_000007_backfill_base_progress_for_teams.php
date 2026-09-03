<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * T-008 — Backfill : les tâches qui ont DÉJÀ une équipe (pivot non vide)
     * et aucune base figée reçoivent leur progression actuelle comme base.
     */
    public function up(): void
    {
        DB::table('tasks as t')
            ->whereNull('t.base_progress_percent')
            ->whereExists(fn($q) => $q
                ->selectRaw('1')
                ->from('task_assignee as ta')
                ->whereColumn('ta.task_id', 't.id'))
            ->update(['t.base_progress_percent' => DB::raw('t.last_progress_percent')]);
    }

    public function down(): void
    {
        // Non réversible : la base des tâches backfillées est conservée.
    }
};