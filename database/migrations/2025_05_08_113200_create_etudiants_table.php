<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('etudiants', function (Blueprint $table) {
$table->id();
$table->string('nom')->nullable();
$table->string('prenom')->nullable();
$table->string('email')->nullable()->unique();
$table->string('telephone')->nullable();
$table->string('ecole')->nullable();
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('etudiants');
}
};
