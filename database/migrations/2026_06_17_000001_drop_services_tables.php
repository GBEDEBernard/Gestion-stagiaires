<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            if (Schema::hasColumn('stages', 'service_id')) {
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
