<?php

namespace App\Http\Controllers;

use App\Http\Requests\Presence\StoreAttendanceEventRequest;
use App\Models\AttendanceDay;
use App\Models\Stage;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PresenceController extends Controller
{
    public function __construct(
        protected PresenceService $presenceService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattache a une fiche etudiant.");

        $activeStage = $etudiant->stages()
            ->with(['site.geofences', 'supervisor'])
            ->whereDate('date_debut', '<=', today())
            ->whereDate('date_fin', '>=', today())
            ->latest('date_debut')
            ->first();

        $attendanceDay = $activeStage
            ? AttendanceDay::where('stage_id', $activeStage->id)
                ->whereDate('attendance_date', today())
                ->first()
            : null;

        return view('presence.index', compact('activeStage', 'attendanceDay'));
    }

    public function checkIn(StoreAttendanceEventRequest $request)
    {
        return $this->handleEvent($request, 'check_in');
    }

    public function checkOut(StoreAttendanceEventRequest $request)
    {
        return $this->handleEvent($request, 'check_out');
    }

    protected function handleEvent(StoreAttendanceEventRequest $request, string $eventType)
    {
        $stage = Stage::with(['site.geofences', 'etudiant'])->findOrFail($request->integer('stage_id'));

        try {
            $event = $eventType === 'check_in'
                ? $this->presenceService->registerCheckIn($stage, $request->user(), $request->validated())
                : $this->presenceService->registerCheckOut($stage, $request->user(), $request->validated());
        } catch (ValidationException $exception) {
            throw $exception;
        }

        $message = $event->status === 'approved'
            ? $event->rejection_reason
            : $event->rejection_reason;

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $event->status,
                'message' => $message,
                'event_id' => $event->id,
            ], $event->status === 'approved' ? 201 : 422);
        }

        return redirect()
            ->route('presence.index')
            ->with($event->status === 'approved' ? 'success' : 'error', $message);
    }
}
