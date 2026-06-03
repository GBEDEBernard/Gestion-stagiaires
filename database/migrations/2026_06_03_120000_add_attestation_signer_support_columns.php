<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_signer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_signer')->default(false)->after('status');
            });
        }

        if (!Schema::hasColumn('signataires', 'user_id')) {
            Schema::table('signataires', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('email')->constrained('users')->nullOnDelete();
            });
        }

        Permission::firstOrCreate([
            'name' => 'signer_attestation',
            'guard_name' => 'web',
        ]);
    }

    public function down(): void
    {
        Permission::where('name', 'signer_attestation')->delete();

        if (Schema::hasColumn('signataires', 'user_id')) {
            Schema::table('signataires', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if (Schema::hasColumn('users', 'is_signer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_signer');
            });
        }
    }
};
