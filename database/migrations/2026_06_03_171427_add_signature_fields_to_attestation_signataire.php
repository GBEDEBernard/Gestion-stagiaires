<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attestation_signataire', function (Blueprint $table) {
            $table->timestamp('signed_at')->nullable()->after('ordre');
            $table->text('signature_data')->nullable()->after('signed_at');
            $table->timestamp('notified_at')->nullable()->after('signature_data');
            $table->string('ip_address', 45)->nullable()->after('notified_at');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down()
    {
        Schema::table('attestation_signataire', function (Blueprint $table) {
            $table->dropColumn(['signed_at', 'signature_data', 'notified_at', 'ip_address', 'user_agent']);
        });
    }
};