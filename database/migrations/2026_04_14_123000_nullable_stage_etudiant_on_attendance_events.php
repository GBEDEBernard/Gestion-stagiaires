<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // stage_id et etudiant_id sont deja nullable dans la migration source.
    }

    public function down(): void
    {
        //
    }
};
