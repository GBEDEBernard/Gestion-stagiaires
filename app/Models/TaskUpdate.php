<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'daily_report_id',
        'updated_by',
        'status',
        'progress_percent',
        'note',
        'happened_at',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
