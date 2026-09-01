<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            $table->string('pointage_token_hash', 64)->nullable()->index()->after('device_fingerprint');
            $table->boolean('is_qr_badge')->default(false)->after('is_primary');
            $table->string('device_name')->nullable()->after('device_label');
            $table->timestamp('token_expires_at')->nullable()->after('revoked_at');
        });
    }

    public function down(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            $table->dropIndex(['pointage_token_hash']);
            $table->dropColumn([
                'pointage_token_hash',
                'is_qr_badge',
                'device_name',
                'token_expires_at',
            ]);
        });
    }
};
