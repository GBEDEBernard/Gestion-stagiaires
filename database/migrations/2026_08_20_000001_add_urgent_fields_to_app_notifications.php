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
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->boolean('is_urgent')->default(false)->index()->after('type');
            $table->string('target_type')->nullable()->after('is_urgent');
            $table->string('target_value')->nullable()->after('target_type');
            $table->string('batch_id')->nullable()->index()->after('target_value');
            $table->foreignId('sender_id')->nullable()->after('batch_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['is_urgent', 'target_type', 'target_value', 'batch_id', 'sender_id']);
        });
    }
};
