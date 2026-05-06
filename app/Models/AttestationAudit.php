<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'attestation_id',
        'stage_id',
        'user_id',
        'action',
        'description',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function attestation()
    {
        return $this->belongsTo(Attestation::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
