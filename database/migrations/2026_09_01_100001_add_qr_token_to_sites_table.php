<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('code');
        });

        // Générer un qr_token unique pour tous les sites existants
        $sites = DB::table('sites')->get();
        foreach ($sites as $site) {
            $token = Str::lower(Str::slug($site->code ?? 'site')) . '-' . Str::random(24);
            DB::table('sites')->where('id', $site->id)->update([
                'qr_token' => $token,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
