<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signataires', function (Blueprint $table) {
            $table->string('email')->nullable()->after('sigle');
            $table->boolean('is_active')->default(true)->after('ordre');
        });
    }

    public function down(): void
    {
        Schema::table('signataires', function (Blueprint $table) {
            $table->dropColumn(['email', 'is_active']);
        });
    }
};
