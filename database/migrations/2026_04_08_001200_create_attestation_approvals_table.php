<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestation_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attestation_id')->constrained('attestations')->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_step');
            $table->string('status')->default('pending');
            $table->text('comment')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['attestation_id', 'approval_step']);
            $table->index(['status', 'decided_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestation_approvals');
    }
};
