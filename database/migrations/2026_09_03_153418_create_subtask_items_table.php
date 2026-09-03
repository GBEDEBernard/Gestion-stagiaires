<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtask_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtask_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subtask_id', 'is_completed']);
            $table->index(['subtask_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtask_items');
    }
};
