<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'task_id',
        'work_type',
        'description',
        'outcome',
        'duration_minutes',
        'progress_percent',
        'display_order',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
