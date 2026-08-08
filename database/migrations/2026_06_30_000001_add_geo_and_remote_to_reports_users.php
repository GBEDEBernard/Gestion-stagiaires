<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-006 — Sécurisation des rapports :
     *  1) daily_reports : capture de la position GPS au moment de la soumission
     *     (latitude, longitude, précision, distance au site, méthode, statut).
     *  2) users : flag « télétravail autorisé » accordé par l'admin.
     */
    public function up(): void
    {
        // --- daily_reports : colonnes géolocalisation ---
        Schema::table('daily_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_reports', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('task_progress_percent');
            }
            if (!Schema::hasColumn('daily_reports', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('daily_reports', 'accuracy_meters')) {
                $table->unsignedInteger('accuracy_meters')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('daily_reports', 'distance_to_site_meters')) {
                $table->unsignedInteger('distance_to_site_meters')->nullable()->after('accuracy_meters');
            }
            if (!Schema::hasColumn('daily_reports', 'location_method')) {
                $table->string('location_method')->nullable()->after('distance_to_site_meters');
            }
            if (!Schema::hasColumn('daily_reports', 'location_verified')) {
                $table->boolean('location_verified')->default(false)->after('location_method');
            }
        });

        // --- users : flag télétravail ---
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'remote_work_enabled')) {
                $table->boolean('remote_work_enabled')->default(false)->after('is_signer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            foreach ([
                'latitude', 'longitude', 'accuracy_meters',
                'distance_to_site_meters', 'location_method', 'location_verified',
            ] as $col) {
                if (Schema::hasColumn('daily_reports', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remote_work_enabled')) {
                $table->dropColumn('remote_work_enabled');
            }
        });
    }
};

