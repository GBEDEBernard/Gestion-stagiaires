<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\WorkScheduleSetting;
use Illuminate\Http\Request;

/**
 * Horaire de référence de l'entreprise. Réservé à l'administrateur : changer
 * l'heure d'arrivée change ce qui compte comme un retard pour tout le monde.
 */
class WorkScheduleSettingController extends Controller
{
    public function edit()
    {
        return view('admin.presence.horaire', [
            'setting' => WorkScheduleSetting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'break_minutes' => 'nullable|integer|min:0|max:480',
        ], [
            'end_time.after' => "L'heure de départ doit être postérieure à l'heure d'arrivée.",
        ]);

        $setting = WorkScheduleSetting::first() ?? new WorkScheduleSetting();

        $setting->fill([
            'start_time'    => $validated['start_time'],
            'end_time'      => $validated['end_time'],
            'break_minutes' => (int) ($validated['break_minutes'] ?? 0),
            'updated_by'    => $request->user()->id,
        ])->save();

        Activity::create([
            'user_id'     => $request->user()->id,
            'action'      => 'Modification horaire de reference',
            'description' => "Horaire de l'entreprise : {$validated['start_time']} – {$validated['end_time']}, pause "
                . (int) ($validated['break_minutes'] ?? 0) . " min.",
        ]);

        return back()->with('success', "Horaire de référence enregistré. Il s'applique aux prochains pointages.");
    }
}
