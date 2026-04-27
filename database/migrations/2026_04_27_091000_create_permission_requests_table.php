<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('domaine_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('first_approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 40);
            $table->string('status', 40)->default('draft');
            $table->string('first_approval_status', 40)->default('pending');
            $table->date('request_date');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->text('first_review_notes')->nullable();
            $table->string('pdf_path')->nullable();
            $table->json('signataires_snapshot')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('first_reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('pdf_generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_requests');
    }
};
