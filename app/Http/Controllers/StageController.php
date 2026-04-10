<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Badge;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Service;
use App\Models\Signataire;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StageController extends Controller
{
    public function index(Request $request)
    {
        $query = Stage::with(['etudiant', 'typestage', 'service', 'site', 'supervisor', 'badge', 'jours']);

        if ($request->filled('statut')) {
            if ($request->statut === 'En cours') {
                $query->whereDate('date_debut', '<=', now())
                    ->whereDate('date_fin', '>=', now());
            } elseif (in_array($request->statut, ['Termine', 'TerminÃ©'], true)) {
                $query->whereDate('date_fin', '<', now());
            } elseif (in_array($request->statut, ['A venir', 'Ã€ venir'], true)) {
                $query->whereDate('date_debut', '>', now());
            }
        }

        if ($request->filled('typestage')) {
            $query->where('typestage_id', $request->typestage);
        }

        $stages = $query->paginate(5)->withQueryString();
        $typestages = TypeStage::all();

        return view('admin.stages.index', compact('stages', 'typestages'));
    }

    public function create()
    {
        $now = now();

        $etudiants = Etudiant::whereDoesntHave('stages', function ($query) use ($now) {
            $query->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        $badges = Badge::whereDoesntHave('stages', function ($query) use ($now) {
            $query->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        $typestages = TypeStage::all();
        $services = Service::all();
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $supervisors = User::role(['admin', 'superviseur'])->orderBy('name')->get();
        $jours = Jour::all();

        return view('admin.stages.create', compact(
            'etudiants',
            'typestages',
            'services',
            'sites',
            'supervisors',
            'badges',
            'jours'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'typestage_id' => 'nullable|exists:typestages,id',
            'service_id' => 'nullable|exists:services,id',
            'site_id' => 'nullable|exists:sites,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'badge_id' => 'nullable|exists:badges,id',
            'theme' => 'nullable|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'jours_id' => 'required|array',
            'jours_id.*' => 'exists:jours,id',
        ]);

        $etudiant = Etudiant::findOrFail($request->etudiant_id);
        $badge = $request->filled('badge_id') ? Badge::findOrFail($request->badge_id) : null;
        $dateDebut = $request->date_debut;
        $dateFin = $request->date_fin;

        $conflictEtudiant = $etudiant->stages()
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($innerQuery) use ($dateDebut, $dateFin) {
                        $innerQuery->where('date_debut', '<=', $dateDebut)
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictEtudiant) {
            return back()->withErrors([
                'etudiant_id' => "Cet etudiant a deja un stage qui chevauche cette periode.",
            ])->withInput();
        }

        if ($badge) {
            $conflictBadge = $badge->stages()
                ->where(function ($query) use ($dateDebut, $dateFin) {
                    $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                        ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                        ->orWhere(function ($innerQuery) use ($dateDebut, $dateFin) {
                            $innerQuery->where('date_debut', '<=', $dateDebut)
                                ->where('date_fin', '>=', $dateFin);
                        });
                })->exists();

            if ($conflictBadge) {
                return back()->withErrors([
                    'badge_id' => 'Ce badge est deja attribue a un autre stage pour ces dates.',
                ])->withInput();
            }
        }

        // jb -> Le stage porte maintenant le pilotage metier:
        // on le rattache directement a son site et a son superviseur.
        $stage = Stage::create($request->only([
            'etudiant_id',
            'typestage_id',
            'service_id',
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
        ]));

        $stage->jours()->sync($request->jours_id);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Creation stage',
            'description' => "Stage {$stage->theme} ajoute pour l'etudiant {$stage->etudiant->nom}",
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage cree avec succes.');
    }

    public function show(Stage $stage)
    {
        $stage->load(['etudiant', 'typestage', 'badge', 'jours', 'service', 'site', 'supervisor']);

        $statutEnCours = $stage->date_debut && $stage->date_fin
            ? (now()->between($stage->date_debut, $stage->date_fin) ? 'En cours' : (now()->gt($stage->date_fin) ? 'Termine' : 'A venir'))
            : 'A venir';

        $signataires = Signataire::orderBy('ordre')->get();
        $etudiant = $stage->etudiant;

        $nombreStages = $etudiant->stages()->count();

        $stagesTermines = $etudiant->stages()
            ->where('id', '!=', $stage->id)
            ->where('date_fin', '<', now())
            ->with(['typestage', 'service'])
            ->orderBy('date_fin', 'desc')
            ->get();

        $attestations = \App\Models\Attestation::whereHas('stage', function ($query) use ($etudiant) {
            $query->where('etudiant_id', $etudiant->id);
        })
            ->with(['stage.typestage', 'signataires'])
            ->orderBy('date_delivrance', 'desc')
            ->get()
            ->map(function ($attestation) {
                if ($attestation->date_delivrance && is_string($attestation->date_delivrance)) {
                    $attestation->date_delivrance = \Carbon\Carbon::parse($attestation->date_delivrance);
                }

                return $attestation;
            });

        $dureeTotale = $etudiant->stages()
            ->whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->get()
            ->sum(function ($item) {
                return $item->date_debut->diffInDays($item->date_fin);
            });

        return view('admin.stages.show', compact(
            'stage',
            'statutEnCours',
            'signataires',
            'nombreStages',
            'stagesTermines',
            'attestations',
            'dureeTotale'
        ));
    }

    public function edit(Stage $stage)
    {
        $etudiants = Etudiant::all();
        $typestages = TypeStage::all();
        $services = Service::all();
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $supervisors = User::role(['admin', 'superviseur'])->orderBy('name')->get();
        $badges = Badge::all();
        $jours = Jour::all();
        $selectedJours = $stage->jours->pluck('id')->toArray();

        return view('admin.stages.edit', compact(
            'stage',
            'etudiants',
            'typestages',
            'services',
            'sites',
            'supervisors',
            'badges',
            'jours',
            'selectedJours'
        ));
    }

    public function update(Request $request, Stage $stage)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'typestage_id' => 'nullable|exists:typestages,id',
            'service_id' => 'nullable|exists:services,id',
            'site_id' => 'nullable|exists:sites,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'badge_id' => 'nullable|exists:badges,id',
            'theme' => 'nullable|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'jours_id' => 'nullable|array',
            'jours_id.*' => 'exists:jours,id',
        ]);

        $etudiant = Etudiant::findOrFail($request->etudiant_id);
        $badge = $request->filled('badge_id') ? Badge::findOrFail($request->badge_id) : null;
        $dateDebut = $request->date_debut;
        $dateFin = $request->date_fin;

        $conflictEtudiant = $etudiant->stages()
            ->where('id', '!=', $stage->id)
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($innerQuery) use ($dateDebut, $dateFin) {
                        $innerQuery->where('date_debut', '<=', $dateDebut)
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictEtudiant) {
            return back()->withErrors([
                'etudiant_id' => "Cet etudiant a deja un stage qui chevauche cette periode.",
            ])->withInput();
        }

        if ($badge) {
            $conflictBadge = $badge->stages()
                ->where('id', '!=', $stage->id)
                ->where(function ($query) use ($dateDebut, $dateFin) {
                    $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                        ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                        ->orWhere(function ($innerQuery) use ($dateDebut, $dateFin) {
                            $innerQuery->where('date_debut', '<=', $dateDebut)
                                ->where('date_fin', '>=', $dateFin);
                        });
                })->exists();

            if ($conflictBadge) {
                return back()->withErrors([
                    'badge_id' => 'Ce badge est deja attribue a un autre stage pour ces dates.',
                ])->withInput();
            }
        }

        $stage->update($request->only([
            'etudiant_id',
            'typestage_id',
            'service_id',
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
        ]));

        $stage->jours()->sync($request->jours_id ?? []);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Mise a jour stage',
            'description' => "Stage {$stage->theme} modifie",
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage mis a jour.');
    }

    public function destroy(Stage $stage)
    {
        $theme = $stage->theme;
        $stage->jours()->detach();
        $stage->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression stage',
            'description' => "Stage {$theme} supprime",
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage supprime.');
    }

    public function servicesDisponibles(Etudiant $etudiant)
    {
        $servicesFaits = $etudiant->stages()->pluck('service_id')->toArray();
        $services = Service::whereNotIn('id', $servicesFaits)->get();

        return response()->json($services);
    }

    public function badge(Stage $stage)
    {
        $stage->load(['etudiant', 'service', 'typestage', 'badge', 'jours']);

        $aujourdHui = now();
        $statutEnCours = $stage->date_debut > $aujourdHui ? 'A venir' : ($stage->date_fin < $aujourdHui ? 'Termine' : 'En cours');

        return view('admin.stages.badge', compact('stage', 'statutEnCours'));
    }

    public function site()
    {
        return response(
            QrCode::size(200)
                ->color(0, 0, 0)
                ->backgroundColor(255, 255, 255)
                ->generate('https://www.tfgbusiness.com'),
            200,
            ['Content-Type' => 'image/svg+xml']
        );
    }

    public function trash()
    {
        $stages = Stage::onlyTrashed()->with(['etudiant', 'typestage', 'badge', 'jours'])->paginate(10);

        return view('admin.stages.corbeille', compact('stages'));
    }

    public function restore($id)
    {
        $stage = Stage::onlyTrashed()->findOrFail($id);
        $stage->restore();

        return redirect()->route('stages.index')->with('success', 'Stage restaure avec succes.');
    }

    public function forceDelete($id)
    {
        $stage = Stage::onlyTrashed()->findOrFail($id);
        $stage->forceDelete();

        return redirect()->route('stages.trash')->with('success', 'Stage supprime definitivement.');
    }
}
