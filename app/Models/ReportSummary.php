<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSummary extends Model
{
    protected $fillable = [
        'user_id',
        'period_type',
        'period_start',
        'period_end',
        'summary',
        'raw_data',
        'model_used',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'raw_data' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
