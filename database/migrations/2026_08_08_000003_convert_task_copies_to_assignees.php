<?php

use App\Models\DailyReport;
use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * T-008 — Convertit les COPIES de tâches (T-007, source_task_id) en
     * assignés PIVOT : chaque copie disparaît, son propriétaire devient
     * assigné de la tâche source, ses rapports sont rattachés à la source.
     */
    public function up(): void
    {
        $copies = Task::whereNotNull('source_task_id')->get(['id', 'source_task_id', 'owner_id']);

        foreach ($copies as $copy) {
            $source = Task::find($copy->source_task_id);

            if (!$source) {
                $copy->delete();
                continue;
            }

            if ($copy->owner_id && $copy->owner_id !== $source->owner_id) {
                DB::table('task_assignee')->updateOrInsert(
                    ['task_id' => $source->id, 'user_id' => $copy->owner_id],
                    ['assigned_at' => now()]
                );
            }

            // Les rapports de la copie rejoignent le fil de la tâche source.
            DailyReport::where('task_id', $copy->id)->update(['task_id' => $source->id]);

            $copy->delete();
        }
    }

    public function down(): void
    {
        // Irréversible : la suppression des copies ne peut pas être reproduite.
    }
};