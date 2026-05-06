<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'attestation_id',
        'stage_id',
        'approver_id',
        'approval_step',
        'status',
        'comment',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function attestation()
    {
        return $this->belongsTo(Attestation::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
