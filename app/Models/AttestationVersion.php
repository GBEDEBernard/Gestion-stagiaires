<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'attestation_id',
        'generated_by',
        'version_number',
        'reference_snapshot',
        'file_path',
        'file_hash',
        'is_current',
        'generated_at',
        'payload',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'is_current' => 'boolean',
        'payload' => 'array',
    ];

    public function attestation()
    {
        return $this->belongsTo(Attestation::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
