<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Services\PointageState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardMissingDepartureController extends Controller
{
    public function __invoke(Request $request, PointageState $pointageState)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['missing_department' => false]);
        }

        $state = $pointageState->forUser($user);
        $hasCheckedIn = $state['day']?->first_check_in_at !== null;
        $missingDeparture = $state['hasForgottenDeparture'] ?? false;

        $flash = [];
        if ($missingDeparture) {
            $day = $state['journeeOubliee'];
            $flash = [
                'missing_departure_day' => $day?->attendance_date?->format('Y-m-d'),
                'missing_departure_label' => $day ? $day->attendance_date->locale('fr')->isoFormat('dddd D MMMM YYYY') : null,
                'missing_departure_action_url' => route('presence.depart-oublie'),
            ];

            Session::now('missing_departure', true);
            Session::now('missing_departure_day', $flash['missing_departure_day']);
        } else {
            Session::forget('missing_departure');
            Session::forget('missing_departure_day');
        }

        return response()->json([
            'missing_departure' => $missingDeparture,
            'checked_in_today' => $hasCheckedIn,
            'flash' => $flash,
        ]);
    }
}
