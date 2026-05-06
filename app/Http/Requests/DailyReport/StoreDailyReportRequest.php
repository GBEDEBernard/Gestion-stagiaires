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
            'items' => ['nullable', 'array'],
            'items.*.task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'items.*.work_type' => ['nullable', 'string', 'max:50'],
            'items.*.description' => ['nullable', 'string', 'max:5000'],
            'items.*.outcome' => ['nullable', 'string', 'max:5000'],
            'items.*.duration_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'items.*.progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
