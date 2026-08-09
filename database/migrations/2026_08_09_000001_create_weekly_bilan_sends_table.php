<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_bilan_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('sent_at')->nullable();
            $table->unique(['user_id', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_bilan_sends');
    }
};