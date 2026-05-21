<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The complete personnels table is created by the earlier
        // 2026_05_15_181832_create_personnels_table migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: this migration intentionally does not own the table.
    }
};
