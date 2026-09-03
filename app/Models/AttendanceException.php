<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceException extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'reason',
        'created_by',
        'permission_request_id',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function permissionRequest()
    {
        return $this->belongsTo(PermissionRequest::class);
    }

    /** Journée excusée automatiquement par une permission approuvée. */
    public function isAutomatic(): bool
    {
        return $this->permission_request_id !== null;
    }
}
