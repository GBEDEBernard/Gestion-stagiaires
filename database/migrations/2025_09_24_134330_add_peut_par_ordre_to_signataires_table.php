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
        Schema::table('signataires', function (Blueprint $table) {
            $table->boolean('peut_par_ordre')->default(false)->after('ordre');
        });
    }

    public function down(): void
    {
        Schema::table('signataires', function (Blueprint $table) {
            $table->dropColumn('peut_par_ordre');
        });
    }
};
