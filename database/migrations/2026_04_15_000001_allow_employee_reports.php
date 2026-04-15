<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Using raw SQL to drop the foreign key first
        Schema::table('daily_reports', function (Blueprint $table) {
            // Get the foreign key name and drop it
            DB::statement('ALTER TABLE `daily_reports` DROP FOREIGN KEY `daily_reports_stage_id_foreign`');
            // Drop the unique constraint
            DB::statement('ALTER TABLE `daily_reports` DROP INDEX `daily_reports_stage_id_report_date_unique`');
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            // Make stage_id and etudiant_id nullable
            $table->unsignedBigInteger('stage_id')->nullable()->change();
            $table->unsignedBigInteger('etudiant_id')->nullable()->change();
            
            // Add user_id for employees
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Re-add the foreign key on stage_id but now nullable
            $table->foreign('stage_id')->references('id')->on('stages')->nullOnDelete();
            
            // Create unique constraints that allow null values
            $table->unique(['stage_id', 'report_date']);
            $table->unique(['user_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            DB::statement('ALTER TABLE `daily_reports` DROP FOREIGN KEY `daily_reports_stage_id_foreign`');
            DB::statement('ALTER TABLE `daily_reports` DROP INDEX `daily_reports_stage_id_report_date_unique`');
            DB::statement('ALTER TABLE `daily_reports` DROP INDEX `daily_reports_user_id_report_date_unique`');
            
            // Drop user_id column
            $table->dropColumn('user_id');
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('stage_id')->change();
            $table->unsignedBigInteger('etudiant_id')->change();
            
            $table->foreign('stage_id')->references('id')->on('stages')->cascadeOnDelete();
            $table->unique(['stage_id', 'report_date']);
        });
    }
};
