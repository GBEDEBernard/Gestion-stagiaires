<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-005 — Rapports :
     *  1) Un rapport peut être un MESSAGE VOCAL (summary devient optionnel si vocal).
     *  2) On passe d'« un rapport par jour par producteur » à « un rapport par
     *     TÂCHE par jour » : chaque tâche a son propre fil de rapports.
     *
     * Idempotent : peut être rejouée sans erreur (DDL MySQL = auto-commit).
     */
    public function up(): void
    {
        // 1) Colonnes vocales (gardées).
        Schema::table('daily_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_reports', 'voice_path')) {
                $table->string('voice_path')->nullable()->after('summary');
            }
            if (!Schema::hasColumn('daily_reports', 'voice_duration')) {
                $table->unsignedInteger('voice_duration')->nullable()->after('voice_path'); // secondes
            }
        });

        // 2) summary optionnel (si rapport uniquement vocal).
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->text('summary')->nullable()->change();
        });

        // 3) Index de support pour les FK avant de pouvoir supprimer les uniques
        //    composites (MySQL refuse de supprimer un index requis par une FK).
        if (!$this->indexExists('daily_reports', 'daily_reports_stage_id_index')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->index('stage_id', 'daily_reports_stage_id_index'));
        }
        if (!$this->indexExists('daily_reports', 'daily_reports_user_id_index')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->index('user_id', 'daily_reports_user_id_index'));
        }

        // 4) Bascule des contraintes : per-producteur/jour -> per-tâche/jour.
        if ($this->indexExists('daily_reports', 'daily_reports_stage_id_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->dropUnique('daily_reports_stage_id_report_date_unique'));
        }
        if ($this->indexExists('daily_reports', 'daily_reports_user_id_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->dropUnique('daily_reports_user_id_report_date_unique'));
        }

        // 5) Unicité par tâche/jour (task_id nullable => rapports hors-tâche non
        //    contraints, protégés par l'anti-doublon applicatif).
        if (!$this->indexExists('daily_reports', 'daily_reports_task_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->unique(['task_id', 'report_date'], 'daily_reports_task_report_date_unique'));
        }
    }

    public function down(): void
    {
        if ($this->indexExists('daily_reports', 'daily_reports_task_report_date_unique')) {
            Schema::table('daily_reports', fn(Blueprint $t) => $t->dropUnique('daily_reports_task_report_date_unique'));
        }

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->unique(['stage_id', 'report_date']);
            $table->unique(['user_id', 'report_date']);
        });

        foreach (['daily_reports_stage_id_index', 'daily_reports_user_id_index'] as $idx) {
            if ($this->indexExists('daily_reports', $idx)) {
                Schema::table('daily_reports', fn(Blueprint $t) => $t->dropIndex($idx));
            }
        }

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['voice_path', 'voice_duration']);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        // Portable MySQL/SQLite (les tests tournent sur sqlite :memory:).
        foreach (Schema::getIndexes($table) as $i) {
            if (($i['name'] ?? null) === $index) {
                return true;
            }
        }

        return false;
    }
};
