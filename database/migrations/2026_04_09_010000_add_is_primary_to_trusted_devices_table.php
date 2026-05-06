<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            // jb -> Le premier appareil vu pour un utilisateur devient
            // son appareil principal de reference pour le pointage.
            $table->boolean('is_primary')->default(false)->after('is_trusted');
        });

        $userIds = DB::table('trusted_devices')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $primaryDeviceId = DB::table('trusted_devices')
                ->where('user_id', $userId)
                ->orderBy('first_seen_at')
                ->orderBy('id')
                ->value('id');

            if ($primaryDeviceId) {
                DB::table('trusted_devices')
                    ->where('id', $primaryDeviceId)
                    ->update(['is_primary' => true]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });
    }
};
