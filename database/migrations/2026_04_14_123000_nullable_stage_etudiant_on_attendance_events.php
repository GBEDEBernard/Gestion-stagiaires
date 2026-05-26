<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE attendance_events MODIFY stage_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE attendance_events MODIFY etudiant_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE attendance_events MODIFY stage_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE attendance_events MODIFY etudiant_id BIGINT UNSIGNED NOT NULL');
    }
};
