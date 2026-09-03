<?php

namespace App\Http\Requests\Presence;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'stage_id' => ['required', 'integer', 'exists:stages,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'device_fingerprint' => ['nullable', 'string', 'max:255'],
            'device_uuid' => ['nullable', 'string', 'max:255'],
            'device_label' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:100'],
            'browser' => ['nullable', 'string', 'max:100'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'stage_id.required' => "Le stage actif est introuvable pour ce pointage.",
            'stage_id.exists' => "Le stage selectionne n'existe plus ou n'est plus accessible.",
            'latitude.required' => "La latitude n'a pas ete recue. Autorise la geolocalisation puis reessaie.",
            'latitude.numeric' => "La latitude recue n'est pas valide.",
            'latitude.between' => "La latitude recue est hors plage autorisee.",
            'longitude.required' => "La longitude n'a pas ete recue. Autorise la geolocalisation puis reessaie.",
            'longitude.numeric' => "La longitude recue n'est pas valide.",
            'longitude.between' => "La longitude recue est hors plage autorisee.",
            'accuracy_meters.integer' => "La precision GPS recue n'est pas valide.",
            'accuracy_meters.min' => "La precision GPS recue n'est pas valide.",
            'accuracy_meters.max' => "La precision GPS recue est trop elevee pour etre exploitable.",
        ];
    }

    public function attributes(): array
    {
        return [
            'stage_id' => 'stage',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'accuracy_meters' => 'precision GPS',
        ];
    }
}
