<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRequestRecipient extends Model
{
    protected $fillable = [
        'permission_request_id', 'signataire_id',
        'status', 'notified_at', 'action_at', 'comment',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'action_at'   => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(PermissionRequest::class, 'permission_request_id');
    }

    public function signataire()
    {
        return $this->belongsTo(Signataire::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'En attente',
            'validated' => 'Validé',
            'rejected'  => 'Refusé',
            'skipped'   => 'Déjà décidé',
            default     => $this->status,
        };
    }
}
