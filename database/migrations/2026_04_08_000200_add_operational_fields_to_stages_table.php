<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            // jb -> Le stage devient le centre de pilotage operationnel:
            // site, superviseur, regles horaires et mode de presence.
            $table->foreignId('site_id')
                ->nullable()
                ->after('service_id')
                ->constrained('sites')
                ->nullOnDelete();

            $table->foreignId('supervisor_id')
                ->nullable()
                ->after('site_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->time('expected_check_in_time')->nullable()->after('date_fin');
            $table->time('expected_check_out_time')->nullable()->after('expected_check_in_time');
            $table->unsignedSmallInteger('allowed_late_minutes')->default(15)->after('expected_check_out_time');
            $table->unsignedSmallInteger('allowed_early_departure_minutes')->default(15)->after('allowed_late_minutes');
            $table->string('presence_mode')->default('geolocation_only')->after('allowed_early_departure_minutes');
            $table->string('follow_up_status')->default('active')->after('presence_mode');
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('site_id');
            $table->dropConstrainedForeignId('supervisor_id');
            $table->dropColumn([
                'expected_check_in_time',
                'expected_check_out_time',
                'allowed_late_minutes',
                'allowed_early_departure_minutes',
                'presence_mode',
                'follow_up_status',
            ]);
        });
    }
};
