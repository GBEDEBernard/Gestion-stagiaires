<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('attestations', function (Blueprint $table) {
$table->id();
// stage_id référence la table EXISTANTE 'stagiaires'
$table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();
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