<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Services\AdminPresenceService;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PresenceController extends Controller
{
    public function __construct(
        private PresenceService $presenceService,
        private AdminPresenceService $adminPresenceService
    ) {
    }

    public function pointage(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        if ($etudiant) {
            $activeStage = $etudiant->stages()
                ->where('date_debut', '<=', now())
                ->where('date_fin', '>=', now())
                ->with('site', 'typestage')
                ->orderByDesc('date_debut')
                ->first();

            if (!$activeStage) {
                return view('presence.pointage', compact('activeStage'));
            }

            $attendanceDay = AttendanceDay::where('stage_id', $activeStage->id)
                ->whereDate('attendance_date', today())
                ->first();

            return view('presence.pointage', compact('activeStage', 'attendanceDay'));
        }

        $domaine = $user->domaine;

        if (!$domaine) {
            abort(403, "Votre compte n'est pas rattaché à un domaine de travail.");
        }

        $activeStage = null;
        $attendanceDay = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        return view('presence.pointage', compact('activeStage', 'attendanceDay', 'domaine'));
    }

    public function prepareCheckIn(Request $request)
    {
        $user = $request->user();

        if ($user->etudiant) {
            return $this->prepareStudentPreview($request, 'check_in');
        }

        return $this->prepareEmployeePreview($request, 'check_in');
    }

    public function prepareCheckOut(Request $request)
    {
        $user = $request->user();

        if ($user->etudiant) {
            return $this->prepareStudentPreview($request, 'check_out');
        }

        return $this->prepareEmployeePreview($request, 'check_out');
    }

    public function showValidation(Request $request)
    {
        $pending = session('pending_pointage');

        if (!$pending) {
            return redirect()->route('presence.pointage')->with('error', 'Aucune donnée de pointage en attente.');
        }

        $user = $request->user();

        if (($pending['user_type'] ?? null) === 'etudiant') {
            $etudiant = $user->etudiant;
            abort_if(!$etudiant, 403, "Votre compte n'est pas rattaché à une fiche étudiant.");

            $stage = $etudiant->stages()->findOrFail($pending['stage_id']);
            $previewContext = $this->presenceService->resolveStagePreviewContext($stage, $pending);

            $previewData = [
                'etudiant_name' => $etudiant->nom . ' ' . $etudiant->prenom,
                'site_name' => $previewContext['site']?->name ?? $stage->site?->name ?? 'Site principal',
                'theme' => $stage->theme,
                'latitude' => $pending['latitude'],
                'longitude' => $pending['longitude'],
                'accuracy' => $pending['accuracy_meters'] ?? 'N/A',
                'pointage_time' => now()->format('H:i'),
                'type' => ($pending['type'] ?? 'check_in') === 'check_in' ? 'arrivée' : 'départ',
                'form_data' => $pending,
            ];

            if ($previewContext['distance'] !== null) {
                $previewData['distance'] = $previewContext['distance'];
            }

            return view('presence.validate', $previewData);
        }

        $domaine = $user->domaine;
        abort_if(!$domaine, 403, "Votre compte n'est pas rattaché à un domaine de travail.");

        $previewContext = $this->presenceService->resolveEmployeePreviewContext($user, $pending);

        $previewData = [
            'user_name' => $user->name,
            'domaine_name' => $domaine->nom,
            'site_name' => $previewContext['site']?->name ?? 'Site principal',
            'latitude' => $pending['latitude'],
            'longitude' => $pending['longitude'],
            'accuracy' => $pending['accuracy_meters'] ?? 'N/A',
            'pointage_time' => now()->format('H:i'),
            'type' => ($pending['type'] ?? 'check_in') === 'check_in' ? 'arrivée' : 'départ',
            'form_data' => $pending,
        ];

        if ($previewContext['distance'] !== null) {
            $previewData['distance'] = $previewContext['distance'];
        }

        return view('presence.validate', $previewData);
    }

    public function confirm(Request $request)
    {
        $pending = session('pending_pointage');

        if (!$pending) {
            return redirect()->route('presence.pointage')->with('error', 'Données de pointage expirées.');
        }

        try {
            $user = $request->user();
            $data = $pending;

            $data['device_uuid'] = $pending['device_uuid'] ?? null;
            $data['device_label'] = $pending['device_label'] ?? null;
            $data['platform'] = $pending['platform'] ?? null;
            $data['browser'] = $pending['browser'] ?? null;
            $data['app_version'] = $pending['app_version'] ?? null;

            if (($pending['user_type'] ?? null) === 'etudiant') {
                $stage = $user->etudiant?->stages()->findOrFail($pending['stage_id']);

                $event = ($pending['type'] ?? 'check_in') === 'check_in'
                    ? $this->presenceService->registerCheckIn($stage, $user, $data)
                    : $this->presenceService->registerCheckOut($stage, $user, $data);
            } else {
                $event = ($pending['type'] ?? 'check_in') === 'check_in'
                    ? $this->presenceService->registerEmployeeCheckIn($user, $data)
                    : $this->presenceService->registerEmployeeCheckOut($user, $data);
            }

            $request->session()->forget('pending_pointage');

            if ($event->status === 'rejected') {
                return redirect()
                    ->route('presence.pointage')
                    ->with('rejection_reason', $event->rejection_reason)
                    ->with('reason_code', $event->reason_code);
            }

            $message = $event->status === 'approved'
                ? '✅ Pointage confirmé et enregistré !'
                : '⚠️ Pointage enregistré mais nécessite validation admin.';

            return redirect()
                ->route('presence.historique')
                ->with('success', $message);
        } catch (\Throwable $exception) {
            Log::error('Pointage confirmation failed: ' . $exception->getMessage(), [
                'user_id' => $request->user()->id,
            ]);

            return redirect()
                ->route('presence.pointage')
                ->with('error', "Erreur lors de l'enregistrement. Réessayez ou contactez l'admin.");
        }
    }

    public function checkIn(Request $request)
    {
        return $this->prepareCheckIn($request);
    }

    public function checkOut(Request $request)
    {
        return $this->prepareCheckOut($request);
    }

    public function historique(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;
        $period = $request->get('period', 'month');

        $userStats = $this->adminPresenceService->getUserDetailedStats($user->id, $period);

        $dateFrom = match ($period) {
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subWeek(),
        };

        $filters = [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            $etudiant ? 'etudiant_id' : 'user_id' => $etudiant ? $etudiant->id : $user->id,
        ];

        $attendanceDays = $this->adminPresenceService
            ->listAttendanceDays($filters, 100)
            ->with(['stage.site', 'anomalies', 'dailyReports'])
            ->get();

        return view('presence.historique', compact('attendanceDays', 'period', 'userStats'));
    }

    public function employeePointage(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->domaine, 403, "Votre compte n'est pas encore rattaché à un domaine.");

        $attendanceDay = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        return view('employee.presence.pointage', compact('attendanceDay', 'user'));
    }

    public function employeeHistorique(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->domaine, 403, "Votre compte n'est pas encore rattaché à un domaine.");

        $period = $request->get('period', 'week');

        $dateFrom = match ($period) {
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subWeek(),
        };

        $filters = [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'user_id' => $user->id,
        ];

        $attendanceDays = $this->adminPresenceService
            ->listAttendanceDays($filters, 100)
            ->with(['anomalies'])
            ->get()
            ->groupBy(fn($day) => $day->attendance_date->format('Y-W'));

        return view('employee.presence.historique', compact('attendanceDays', 'period'));
    }

    private function prepareStudentPreview(Request $request, string $eventType)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        abort_if(!$etudiant, 403, "Votre compte n'est pas rattaché à une fiche étudiant.");

        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy_meters' => 'nullable|numeric|min:0',
            'device_fingerprint' => 'required|string',
        ]);

        $stage = $etudiant->stages()->findOrFail($request->stage_id);
        $previewContext = $this->presenceService->resolveStagePreviewContext($stage, $request->only([
            'latitude',
            'longitude',
            'accuracy_meters',
        ]));

        $previewData = [
            'etudiant_name' => $etudiant->nom . ' ' . $etudiant->prenom,
            'site_name' => $previewContext['site']?->name ?? $stage->site?->name ?? 'Site principal',
            'theme' => $stage->theme,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy_meters ?? 'N/A',
            'pointage_time' => now()->format('H:i'),
            'type' => $eventType === 'check_in' ? 'arrivée' : 'départ',
        ];

        if ($previewContext['distance'] !== null) {
            $previewData['distance'] = $previewContext['distance'];
        }

        session(['pending_pointage' => [
            'type' => $eventType,
            'stage_id' => $request->stage_id,
            'user_type' => 'etudiant',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy_meters' => $request->accuracy_meters,
            'resolved_site_id' => $previewContext['site']?->id,
            'resolved_site_geofence_id' => $previewContext['geofence']?->id,
            'device_fingerprint' => $request->device_fingerprint,
            'device_uuid' => $request->device_uuid ?? '',
            'device_label' => $request->device_label ?? '',
            'platform' => $request->platform ?? '',
            'browser' => $request->browser ?? '',
            'app_version' => $request->app_version ?? '',
        ]]);

        return view('presence.validate', $previewData);
    }

    private function prepareEmployeePreview(Request $request, string $eventType)
    {
        $user = $request->user();
        $domaine = $user->domaine;

        abort_if(!$domaine, 403, "Votre compte n'est pas rattaché à un domaine de travail.");

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy_meters' => 'nullable|numeric|min:0',
            'device_fingerprint' => 'required|string',
        ]);

        $previewContext = $this->presenceService->resolveEmployeePreviewContext($user, $request->only([
            'latitude',
            'longitude',
            'accuracy_meters',
        ]));

        $previewData = [
            'user_name' => $user->name,
            'domaine_name' => $domaine->nom,
            'site_name' => $previewContext['site']?->name ?? 'Site principal',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy_meters ?? 'N/A',
            'pointage_time' => now()->format('H:i'),
            'type' => $eventType === 'check_in' ? 'arrivée' : 'départ',
        ];

        if ($previewContext['distance'] !== null) {
            $previewData['distance'] = $previewContext['distance'];
        }

        session(['pending_pointage' => [
            'type' => $eventType,
            'user_id' => $user->id,
            'user_type' => 'employe',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy_meters' => $request->accuracy_meters,
            'resolved_site_id' => $previewContext['site']?->id,
            'resolved_site_geofence_id' => $previewContext['geofence']?->id,
            'device_fingerprint' => $request->device_fingerprint,
            'device_uuid' => $request->device_uuid ?? '',
            'device_label' => $request->device_label ?? '',
            'platform' => $request->platform ?? '',
            'browser' => $request->browser ?? '',
            'app_version' => $request->app_version ?? '',
        ]]);

        return view('presence.validate', $previewData);
    }
}
