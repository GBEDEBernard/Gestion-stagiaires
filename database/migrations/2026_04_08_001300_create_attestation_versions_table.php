<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestation_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attestation_id')->constrained('attestations')->cascadeOnDelete();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('reference_snapshot')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_hash')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamp('generated_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['attestation_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestation_versions');
    }
};
