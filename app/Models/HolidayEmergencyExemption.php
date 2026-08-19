<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayEmergencyExemption extends Model
{
    protected $fillable = [
        'holiday_id',
        'user_id',
        'message',
        'called_by',
    ];

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'called_by');
    }

    public static function isExempted(User $user, $date = null): bool
    {
        $date = $date ?: today();
        return static::where('user_id', $user->id)
            ->whereHas('holiday', function ($q) use ($date) {
                $q->whereDate('date', $date)->where('is_active', true);
            })
            ->exists();
    }
}
