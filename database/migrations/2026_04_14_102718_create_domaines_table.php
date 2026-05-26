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
        Schema::create('domaines', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('domaine_id')
                ->references('id')
                ->on('domaines')
                ->nullOnDelete();
        });

        Schema::table('stages', function (Blueprint $table) {
            $table->foreign('domaine_id')
                ->references('id')
                ->on('domaines')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropForeign(['domaine_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['domaine_id']);
        });

        Schema::dropIfExists('domaines');
    }
};
