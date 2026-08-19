<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-008 — Rapports sur tâche partagée : la contrainte « un rapport par
     * TÂCHE/jour » (T-005) devient « un rapport par TÂCHE par PERSONNE par jour »,
     * pour que chaque assigné dépose le sien sur la même tâche.
     */
    public function up(): void
    {
        if ($this->indexExists('daily_reports', 'daily_reports_task_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->dropUnique('daily_reports_task_report_date_unique'));
        }

        if (!$this->indexExists('daily_reports', 'daily_reports_task_user_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->unique(
                ['task_id', 'user_id', 'report_date'],
                'daily_reports_task_user_report_date_unique'
            ));
        }
    }

    public function down(): void
    {
        if ($this->indexExists('daily_reports', 'daily_reports_task_user_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->dropUnique('daily_reports_task_user_report_date_unique'));
        }

        if (!$this->indexExists('daily_reports', 'daily_reports_task_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->unique(
                ['task_id', 'report_date'],
                'daily_reports_task_report_date_unique'
            ));
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        foreach (Schema::getIndexes($table) as $i) {
            if (($i['name'] ?? null) === $index) {
                return true;
            }
        }

        return false;
    }
};