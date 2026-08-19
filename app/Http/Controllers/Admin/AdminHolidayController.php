<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmergencyCallMail;
use App\Mail\HolidayPublishedMail;
use App\Models\Holiday;
use App\Models\HolidayEmergencyExemption;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminHolidayController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $holidays = Holiday::with('creator', 'exemptions.user')
            ->withCount('exemptions')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $activeUsersCount = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['employe', 'etudiant', 'fonctionnaire']);
        })->where('status', 'actif')->count();

        $exemptionsByHoliday = $holidays->mapWithKeys(function ($holiday) {
            return [$holiday->id => $holiday->exemptions->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->user?->name ?? 'Utilisateur supprimé',
                'email' => $e->user?->email ?? '',
                'message' => $e->message ?? '',
            ])];
        });

        return view('admin.holidays.index', compact('holidays', 'activeUsersCount', 'exemptionsByHoliday'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = $request->user()->id;

        Holiday::create($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Jour férié ajouté avec succès.');
    }

    public function edit(Holiday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Jour férié mis à jour.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Jour férié supprimé.');
    }

    public function toggle(Holiday $holiday)
    {
        $holiday->is_active = !$holiday->is_active;
        $holiday->save();

        if ($holiday->is_active) {
            $this->notifyEmployees($holiday);
            $holiday->notified = true;
        } else {
            $holiday->notified = false;
        }
        $holiday->save();

        $status = $holiday->is_active ? 'activé' : 'désactivé';
        return redirect()->route('admin.holidays.index')
            ->with('success', "Jour férié « {$holiday->label} » {$status}.");
    }

    public function notify(Request $request, Holiday $holiday)
    {
        if (!$holiday->is_active) {
            return redirect()->route('admin.holidays.index')
                ->with('error', 'Activez d\'abord le jour férié avant d\'envoyer la notification.');
        }

        $this->notifyEmployees($holiday);
        $holiday->notified = true;
        $holiday->save();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Notification publiée et envoyée à tous les utilisateurs actifs (email + in-app).');
    }

    /**
     * Révoque une autorisation d'urgence déjà accordée.
     */
    public function revokeExemption(HolidayEmergencyExemption $exemption)
    {
        $user = $exemption->user;
        $holiday = $exemption->holiday;

        $exemption->delete();

        if ($user) {
            $formattedDate = $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY');
            $this->notificationService->push(
                userId: $user->id,
                type: 'urgence_jour_ferie',
                title: '❌ Appel d\'urgence annulé',
                message: "Votre appel d'urgence du {$formattedDate} ({$holiday->label}) a été annulé. Vous ne devez plus vous présenter ce jour-là.",
                url: route('presence.pointage'),
                icon: 'x-circle',
                color: 'gray'
            );
        }

        $revokedName = $user?->name ?? 'l\'utilisateur';
        return redirect()->route('admin.holidays.index')
            ->with('success', "Autorisation d'urgence révoquée pour $revokedName.");
    }

    public function emergencyCall(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $formattedDate = $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY');
        $customMsg = $validated['message'] ?? 'Veuillez vous présenter pour une intervention urgente.';

        foreach ($users as $user) {
            HolidayEmergencyExemption::firstOrCreate([
                'holiday_id' => $holiday->id,
                'user_id' => $user->id,
            ], [
                'message' => $customMsg,
                'called_by' => $request->user()->id,
            ]);

            // In-app notification
            $this->notificationService->push(
                userId: $user->id,
                type: 'urgence_jour_ferie',
                title: '🚨 Intervention urgente - ' . $holiday->label,
                message: "Vous êtes appelé(e) pour une intervention d'urgence le {$formattedDate}. {$customMsg}",
                url: route('presence.pointage'),
                icon: 'exclamation-triangle',
                color: 'red'
            );

            $this->notificationService->push(
                userId: $user->id,
                type: 'urgence_jour_ferie',
                title: '🔓 Permission spéciale',
                message: "Vous avez été autorisé(e) à pointer le {$formattedDate} (jour férié). Présentez-vous au site.",
                url: route('presence.pointage'),
                icon: 'lock-open',
                color: 'amber'
            );

            // Email notification
            Mail::to($user->getEmailForVerification())
                ->send(new EmergencyCallMail($user, $holiday, $customMsg));
        }

        $count = $users->count();
        return redirect()->route('admin.holidays.index')
            ->with('success', "Appel d'urgence envoyé à {$count} personne(s) avec notification par email et exemption de pointage.");
    }

    public function usersList(Request $request)
    {
        $search = $request->get('q', '');
        $role = $request->get('role', '');

        $query = User::with('personnel')
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['employe', 'etudiant', 'fonctionnaire']);
            })
            ->where('status', 'actif');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('personnel', function ($p) use ($search) {
                      $p->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        if ($role) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->limit(50)->get()->map(function ($user) {
            $role = $user->roles->first()?->name;
            $label = match ($role) {
                'etudiant' => 'Stagiaire',
                'employe' => 'Employé',
                'fonctionnaire' => 'Fonctionnaire',
                default => $role,
            };
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $label,
                'role_name' => $role,
            ];
        });

        return response()->json($users);
    }

    private function notifyEmployees(Holiday $holiday): void
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['employe', 'etudiant', 'fonctionnaire']);
        })
            ->where('status', 'actif')
            ->get();

        $formattedDate = $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY');

        foreach ($users as $user) {
            $this->notificationService->push(
                userId: $user->id,
                type: 'jour_ferie',
                title: '📅 Jour férié : ' . $holiday->label,
                message: "Le {$formattedDate} est déclaré jour férié ({$holiday->label}). "
                    . "Vous ne devez pas pointer ce jour. "
                    . "En cas d'urgence, votre responsable peut vous contacter.",
                url: route('presence.pointage'),
                icon: 'calendar',
                color: 'purple'
            );

            Mail::to($user->getEmailForVerification())
                ->send(new HolidayPublishedMail($user, $holiday));
        }
    }
}
