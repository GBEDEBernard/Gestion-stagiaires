<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();
            $table->foreignId('typestage_id')->nullable()->constrained('typestages')->nullOnDelete(); // 🔗 type stage
            $table->string('reference')->unique()->nullable(); // Réf générée ex: "ATS 02_25/TFG/DG/DT"
            $table->date('date_delivrance');
            $table->string('fichier_pdf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestations');
    }
};
