<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyBilanSend extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}