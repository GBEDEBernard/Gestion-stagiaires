<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestation_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attestation_id')->constrained('attestations')->cascadeOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['attestation_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestation_audits');
    }
};
