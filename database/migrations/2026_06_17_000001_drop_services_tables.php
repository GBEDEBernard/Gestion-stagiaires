<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            if (Schema::hasColumn('stages', 'service_id')) {
                $foreignKey = 'stages_service_id_foreign';
                $hasForeignKey = collect(DB::select('SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?', [DB::getDatabaseName(), 'stages', $foreignKey]))->isNotEmpty();

                if ($hasForeignKey) {
                    $table->dropForeign(['service_id']);
                }
                $table->dropColumn('service_id');
            }
        });

        Schema::dropIfExists('services');
    }

    public function down(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('stages', function (Blueprint $table) {
            $table->foreignId('service_id')
                ->nullable()
                ->index();
        });
    }
};
