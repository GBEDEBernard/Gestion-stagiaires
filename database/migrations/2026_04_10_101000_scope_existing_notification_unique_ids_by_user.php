<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $notifications = DB::table('app_notifications')
            ->select('id', 'unique_id', 'user_id')
            ->orderBy('id')
            ->get();

        foreach ($notifications as $notification) {
            $scopedUniqueId = str_contains($notification->unique_id, '_user_')
                ? $notification->unique_id
                : "{$notification->unique_id}_user_{$notification->user_id}";

            // jb -> On remet les anciennes notifications au meme format
            // que le nouveau generateur pour supprimer la collision source.
            DB::table('app_notifications')
                ->where('id', $notification->id)
                ->update(['unique_id' => $scopedUniqueId]);
        }
    }

    public function down(): void
    {
        $notifications = DB::table('app_notifications')
            ->select('id', 'unique_id')
            ->orderBy('id')
            ->get();

        foreach ($notifications as $notification) {
            $unscopedUniqueId = preg_replace('/_user_\d+$/', '', $notification->unique_id);

            DB::table('app_notifications')
                ->where('id', $notification->id)
                ->update(['unique_id' => $unscopedUniqueId]);
        }
    }
};
