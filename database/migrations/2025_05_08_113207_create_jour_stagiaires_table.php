<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('jour_stagiaire')) {
            Schema::create('jour_stagiaire', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stagiaire_id')->constrained()->onDelete('cascade');
                $table->foreignId('jour_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }
   
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jour_stagiaire');
    }
};
