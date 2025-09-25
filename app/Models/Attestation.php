<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attestation extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'typestage_id',
        'reference',
        'date_delivrance',
        'fichier_pdf',
    ];

    // Relations
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function typestage()
    {
        return $this->belongsTo(TypeStage::class);
    }

    public function signataires()
{
    return $this->belongsToMany(Signataire::class, 'attestation_signataire')
                ->withPivot('par_ordre','ordre')
                ->withTimestamps();
}

}
