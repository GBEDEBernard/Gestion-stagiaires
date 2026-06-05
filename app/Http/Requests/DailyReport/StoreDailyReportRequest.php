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
        // T-005 : un rapport peut être uniquement vocal → résumé optionnel si vocal.
        $hasVoice = $this->hasFile('voice');

        return [
            'status_action' => ['required', 'in:draft,submit'],
            'summary' => [$hasVoice ? 'nullable' : 'required', 'string', 'max:5000'],
            'blockers' => ['nullable', 'string', 'max:5000'],
            'next_steps' => ['nullable', 'string', 'max:5000'],
            'hours_declared' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'completion_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            // T-003 : une seule tâche par rapport + progression déclarée
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'task_progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            // T-005 : rapport vocal (MediaRecorder) + durée en secondes
            'voice' => ['nullable', 'file', 'max:10240'],
            'voice_duration' => ['nullable', 'integer', 'min:0', 'max:36000'],
        ];
    }

    public function messages(): array
    {
        return [
            'summary.required' => 'Ajoute un résumé écrit ou enregistre un message vocal.',
        ];
    }
}
