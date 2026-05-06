<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('shield');
            $table->string('color')->default('blue');
            $table->text('description')->nullable();
            $table->json('fields_config');
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('permission_types')->insert([
            [
                'name'         => 'Absence',
                'slug'         => 'absence',
                'icon'         => 'calendar-x',
                'color'        => 'red',
                'description'  => "Demande d'absence pour une ou plusieurs journées",
                'fields_config' => json_encode([
                    ['key' => 'start_date', 'label' => 'Date de début',   'type' => 'date',     'required' => true],
                    ['key' => 'end_date',   'label' => 'Date de fin',      'type' => 'date',     'required' => true],
                    ['key' => 'motif',      'label' => 'Motif détaillé',   'type' => 'textarea', 'required' => true],
                ]),
                'sort_order'   => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Retard',
                'slug'         => 'retard',
                'icon'         => 'clock',
                'color'        => 'amber',
                'description'  => "Justification d'un retard à l'arrivée",
                'fields_config' => json_encode([
                    ['key' => 'date',       'label' => 'Date du retard',            'type' => 'date',     'required' => true],
                    ['key' => 'start_time', 'label' => "Heure d'arrivée prévue",    'type' => 'time',     'required' => true],
                    ['key' => 'end_time',   'label' => "Heure d'arrivée effective", 'type' => 'time',     'required' => true],
                    ['key' => 'motif',      'label' => 'Motif du retard',           'type' => 'textarea', 'required' => true],
                ]),
                'sort_order'   => 2,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Départ anticipé',
                'slug'         => 'depart-anticipe',
                'icon'         => 'log-out',
                'color'        => 'orange',
                'description'  => "Permission de quitter avant l'heure officielle",
                'fields_config' => json_encode([
                    ['key' => 'date',           'label' => 'Date',                      'type' => 'date',     'required' => true],
                    ['key' => 'departure_time', 'label' => 'Heure de départ souhaitée', 'type' => 'time',     'required' => true],
                    ['key' => 'motif',          'label' => 'Motif',                     'type' => 'textarea', 'required' => true],
                ]),
                'sort_order'   => 3,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Congé exceptionnel',
                'slug'         => 'conge-exceptionnel',
                'icon'         => 'gift',
                'color'        => 'purple',
                'description'  => 'Demande de congé pour raison exceptionnelle',
                'fields_config' => json_encode([
                    ['key' => 'start_date', 'label' => 'Date de début',          'type' => 'date',     'required' => true],
                    ['key' => 'end_date',   'label' => 'Date de fin',             'type' => 'date',     'required' => true],
                    ['key' => 'motif',      'label' => 'Motif et justification',  'type' => 'textarea', 'required' => true],
                ]),
                'sort_order'   => 4,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_types');
    }
};
