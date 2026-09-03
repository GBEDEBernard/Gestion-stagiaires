<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->string('final_report_path')->nullable()->after('livrables');
            $table->timestamp('final_report_uploaded_at')->nullable()->after('final_report_path');
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn(['final_report_path', 'final_report_uploaded_at']);
        });
    }
};
