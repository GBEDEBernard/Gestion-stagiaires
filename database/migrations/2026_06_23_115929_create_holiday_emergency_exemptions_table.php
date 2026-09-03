<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holiday_emergency_exemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->foreignId('called_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['holiday_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_emergency_exemptions');
    }
};
