<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_request_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('signataire_id')->constrained('signataires')->cascadeOnDelete();
            $table->enum('status', ['pending', 'validated', 'rejected', 'skipped'])->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('action_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['permission_request_id', 'signataire_id'], 'prr_request_signataire_unique');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('permission_request_recipients');
    }
};
