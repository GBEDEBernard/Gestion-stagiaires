<?php

namespace App\Http\Requests\DailyReport;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status_action' => ['required', 'in:draft,submit'],
            'summary' => ['required', 'string', 'max:5000'],
            'blockers' => ['nullable', 'string', 'max:5000'],
            'next_steps' => ['nullable', 'string', 'max:5000'],
            'hours_declared' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'completion_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            // T-003 : une seule tâche par rapport + progression déclarée
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'task_progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
