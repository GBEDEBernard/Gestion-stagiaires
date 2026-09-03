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
            'introduction' => ['nullable', 'string', 'max:5000'],
            'summary' => ['required_if:status_action,submit', 'nullable', 'string', 'max:5000'],
            'blockers' => ['nullable', 'string', 'max:5000'],
            'next_steps' => ['nullable', 'string', 'max:5000'],
            'hours_declared' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'completion_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'completed_subtask_ids' => ['nullable', 'array'],
            'completed_subtask_ids.*' => ['integer', 'exists:subtasks,id'],
            'task_progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0'],
            'location_method' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'summary.required_if' => 'Le résumé du travail réalisé est requis lors de la soumission.',
        ];
    }
}
