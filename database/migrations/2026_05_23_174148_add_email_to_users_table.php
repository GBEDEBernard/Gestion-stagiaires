<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable()->after('personnel_id');
            });
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::table('users')
            ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
            ->where(function ($query) {
                $query->whereNull('users.email')
                    ->orWhere('users.email', '');
            })
            ->whereNotNull('personnels.email')
            ->update([
                'users.email' => DB::raw('personnels.email'),
            ]);

        DB::table('users')
            ->where(function ($query) {
                $query->whereNull('email')
                    ->orWhere('email', '');
            })
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['email' => "user-{$user->id}@local.invalid"]);
            });

        if (!$this->emailUniqueIndexExists()) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('email');
            });
        }
    }

    public function down()
    {
        // The users.email column belongs to the original auth schema in most
        // installs, so rollback must not drop it.
        if ($this->emailUniqueIndexExists()) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_email_unique');
            });
        }
    }

    private function emailUniqueIndexExists(): bool
    {
        return collect(Schema::getIndexes('users'))
            ->contains(fn(array $index) => ($index['name'] ?? null) === 'users_email_unique');
    }
};
