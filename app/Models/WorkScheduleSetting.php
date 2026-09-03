<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Horaire de référence de l'entreprise. Une seule ligne, modifiable par
 * l'administrateur : il s'applique aux employés et à tout stage qui ne déclare
 * pas le sien.
 */
class WorkScheduleSetting extends Model
{
    protected $table = 'work_schedule_settings';

    protected $fillable = ['start_time', 'end_time', 'break_minutes', 'updated_by'];

    protected $casts = ['break_minutes' => 'integer'];

    /**
     * Dernier recours si la table est vide — installation neuve dont le seeder
     * n'a pas tourné. Ces valeurs ne sont jamais un réglage, seulement un filet.
     */
    public const FALLBACK = [
        'start_time'    => '08:00',
        'end_time'      => '18:00',
        'break_minutes' => 120,
    ];

    public static function current(): self
    {
        return static::first() ?? new self(self::FALLBACK);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
