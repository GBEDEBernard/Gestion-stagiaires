<?php

namespace App\Http\Requests\Presence;

use Illuminate\Foundation\Http\FormRequest;

class ResolveAnomalyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('presence.admin.anomalies.review');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'resolution_note' => 'nullable|string|max:1000',
            'reviewed_by' => 'sometimes|exists:users,id',
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'resolution_note.max' => 'La note de résolution ne peut dépasser 1000 caractères.',
        ];
    }
}
