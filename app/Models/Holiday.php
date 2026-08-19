<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'date',
        'label',
        'description',
        'is_active',
        'notified',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'notified' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exemptions()
    {
        return $this->hasMany(HolidayEmergencyExemption::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public static function isHoliday($date = null)
    {
        $date = $date ?: today();
        return static::whereDate('date', $date)->where('is_active', true)->exists();
    }

    public static function todayIsHoliday(): bool
    {
        return static::isHoliday(today());
    }
}
