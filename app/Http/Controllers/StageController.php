<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Stage;
use App\Models\Etudiant;
use App\Models\TypeStage;
use App\Models\Service;
use App\Models\Badge;
use App\Models\Jour;
use App\Models\Activity;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Signataire;

class StageController extends Controller
{
    // Liste des stages
    public function index(Request $request)
    {
        $query = Stage::with(['etudiant', 'typestage', 'service', 'badge', 'jours']);

        if ($request->filled('statut')) {
            if ($request->statut == 'En cours') {
                $query->whereDate('date_debut', '<=', now())
                    ->whereDate('date_fin', '>=', now());
            } elseif ($request->statut == 'Terminé') {
                $query->whereDate('date_fin', '<', now());
            } elseif ($request->statut == 'À venir') {
=======
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
>>>>>>> e9635ab
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

<<<<<<< HEAD
    // Formulaire de création des stagiaire
=======
>>>>>>> e9635ab
    public function create()
    {
        $now = now();

<<<<<<< HEAD
        $etudiants = Etudiant::whereDoesntHave('stages', function ($q) use ($now) {
            $q->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        $badges = Badge::whereDoesntHave('stages', function ($q) use ($now) {
            $q->where('date_debut', '<=', $now)
=======
        $etudiants = Etudiant::whereDoesntHave('stages', function ($query) use ($now) {
            $query->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        $badges = Badge::whereDoesntHave('stages', function ($query) use ($now) {
            $query->where('date_debut', '<=', $now)
>>>>>>> e9635ab
                ->where('date_fin', '>=', $now);
        })->get();

        $typestages = TypeStage::all();
<<<<<<< HEAD
        $services   = Service::all();
        $jours      = Jour::all();

        return view('admin.stages.create', compact('etudiants', 'typestages', 'services', 'badges', 'jours'));
    }

    // Enregistrer un stage
    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id'  => 'required|exists:etudiants,id',
            'typestage_id' => 'nullable|exists:typestages,id',
            'service_id'   => 'nullable|exists:services,id',
            'badge_id'     => 'nullable|exists:badges,id',
            'theme'        => 'nullable|string|max:255',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'jours_id'     => 'required|array',
            'jours_id.*'   => 'exists:jours,id',
        ]);

        $etudiant = Etudiant::findOrFail($request->etudiant_id);
        $badge    = Badge::findOrFail($request->badge_id);
        $dateDebut = $request->date_debut;
        $dateFin   = $request->date_fin;

        // Vérification des conflits de stage pour l'étudiant
        $conflictEtudiant = $etudiant->stages()
            ->where(function ($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($q2) use ($dateDebut, $dateFin) {
                        $q2->where('date_debut', '<=', $dateDebut)
=======
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
>>>>>>> e9635ab
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictEtudiant) {
<<<<<<< HEAD
            return back()->withErrors(['etudiant_id' => 'Cet étudiant a déjà un stage qui chevauche cette période.'])->withInput();
        }

        // Vérification des conflits de badge
        $conflictBadge = $badge->stages()
            ->where(function ($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($q2) use ($dateDebut, $dateFin) {
                        $q2->where('date_debut', '<=', $dateDebut)
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictBadge) {
            return back()->withErrors([
                'badge_id' => 'Ce badge est déjà attribué à un autre stage pour ces dates.'
            ])->withInput();
        }

=======
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
>>>>>>> e9635ab
        $stage = Stage::create($request->only([
            'etudiant_id',
            'typestage_id',
            'service_id',
<<<<<<< HEAD
            'badge_id',
            'theme',
            'date_debut',
            'date_fin'
=======
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
>>>>>>> e9635ab
        ]));

        $stage->jours()->sync($request->jours_id);

        Activity::create([
<<<<<<< HEAD
            'user_id'     => auth()->id(),
            'action'      => 'Création stage',
            'description' => "Stage {$stage->theme} ajouté pour l'étudiant {$stage->etudiant->nom}"
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage créé avec succès.');
    }

    // Vue show avec model binding
    public function show(Stage $stage)
    {
        $stage->load(['etudiant', 'typestage', 'badge', 'jours', 'service']);

        $statutEnCours = $stage->date_debut && $stage->date_fin
            ? (now()->between($stage->date_debut, $stage->date_fin) ? 'En cours' : (now()->gt($stage->date_fin) ? 'Terminé' : 'À venir'))
            : 'À venir';

        $signataires = Signataire::orderBy('ordre')->get();

        // === NOUVELLE DONNÉE : Historique de l'étudiant ===
        $etudiant = $stage->etudiant;

        // Nombre total de stages de l'étudiant
        $nombreStages = $etudiant->stages()->count();

        // Stages complétés (terminés) - en excluant le stage actuel
=======
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

>>>>>>> e9635ab
        $stagesTermines = $etudiant->stages()
            ->where('id', '!=', $stage->id)
            ->where('date_fin', '<', now())
            ->with(['typestage', 'service'])
            ->orderBy('date_fin', 'desc')
            ->get();

<<<<<<< HEAD
        // Toutes les attestations de l'étudiant (via ses stages)
=======
>>>>>>> e9635ab
        $attestations = \App\Models\Attestation::whereHas('stage', function ($query) use ($etudiant) {
            $query->where('etudiant_id', $etudiant->id);
        })
            ->with(['stage.typestage', 'signataires'])
            ->orderBy('date_delivrance', 'desc')
            ->get()
            ->map(function ($attestation) {
<<<<<<< HEAD
                // Convertir la date en objet Carbon si c'est une chaîne
                if ($attestation->date_delivrance && is_string($attestation->date_delivrance)) {
                    $attestation->date_delivrance = \Carbon\Carbon::parse($attestation->date_delivrance);
                }
                return $attestation;
            });

        // Durée totale des stages (en jours)
=======
                if ($attestation->date_delivrance && is_string($attestation->date_delivrance)) {
                    $attestation->date_delivrance = \Carbon\Carbon::parse($attestation->date_delivrance);
                }

                return $attestation;
            });

>>>>>>> e9635ab
        $dureeTotale = $etudiant->stages()
            ->whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->get()
<<<<<<< HEAD
            ->sum(function ($s) {
                return $s->date_debut->diffInDays($s->date_fin);
=======
            ->sum(function ($item) {
                return $item->date_debut->diffInDays($item->date_fin);
>>>>>>> e9635ab
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

<<<<<<< HEAD
    // Formulaire d'édition avec model binding
=======
>>>>>>> e9635ab
    public function edit(Stage $stage)
    {
        $etudiants = Etudiant::all();
        $typestages = TypeStage::all();
<<<<<<< HEAD
        $services   = Service::all();
        $badges     = Badge::all();
        $jours      = Jour::all();
=======
        $services = Service::all();
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $supervisors = User::role(['admin', 'superviseur'])->orderBy('name')->get();
        $badges = Badge::all();
        $jours = Jour::all();
>>>>>>> e9635ab
        $selectedJours = $stage->jours->pluck('id')->toArray();

        return view('admin.stages.edit', compact(
            'stage',
            'etudiants',
            'typestages',
            'services',
<<<<<<< HEAD
=======
            'sites',
            'supervisors',
>>>>>>> e9635ab
            'badges',
            'jours',
            'selectedJours'
        ));
    }

<<<<<<< HEAD
    // Mise à jour avec model binding
    public function update(Request $request, Stage $stage)
    {

        $request->validate([
            'etudiant_id'  => 'required|exists:etudiants,id',
            'typestage_id' => 'nullable|exists:typestages,id',
            'service_id'   => 'nullable|exists:services,id',
            'badge_id'     => 'nullable|exists:badges,id',
            'theme'        => 'nullable|string|max:255',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'jours_id'     => 'nullable|array',
            'jours_id.*'   => 'exists:jours,id',
        ]);

        $etudiant = Etudiant::findOrFail($request->etudiant_id);
        $badge    = Badge::findOrFail($request->badge_id);
        $dateDebut = $request->date_debut;
        $dateFin   = $request->date_fin;

        // Vérification des conflits pour l'étudiant (hors stage courant)
        $conflictEtudiant = $etudiant->stages()
            ->where('id', '!=', $stage->id)
            ->where(function ($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($q2) use ($dateDebut, $dateFin) {
                        $q2->where('date_debut', '<=', $dateDebut)
=======
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
>>>>>>> e9635ab
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictEtudiant) {
<<<<<<< HEAD
            return back()->withErrors(['etudiant_id' => 'Cet étudiant a déjà un stage qui chevauche cette période.'])->withInput();
        }

        // Vérification des conflits de badge (hors stage courant)
        $conflictBadge = $badge->stages()
            ->where('id', '!=', $stage->id)
            ->where(function ($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($q2) use ($dateDebut, $dateFin) {
                        $q2->where('date_debut', '<=', $dateDebut)
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictBadge) {
            return back()->withErrors(['badge_id' => 'Ce badge est déjà attribué à un autre stage pour ces dates.'])->withInput();
=======
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
>>>>>>> e9635ab
        }

        $stage->update($request->only([
            'etudiant_id',
            'typestage_id',
            'service_id',
<<<<<<< HEAD
            'badge_id',
            'theme',
            'date_debut',
            'date_fin'
=======
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
>>>>>>> e9635ab
        ]));

        $stage->jours()->sync($request->jours_id ?? []);

        Activity::create([
<<<<<<< HEAD
            'user_id'     => auth()->id(),
            'action'      => 'Mise à jour stage',
            'description' => "Stage {$stage->theme} modifié"
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage mis à jour.');
    }

    // Supprimer avec model binding
=======
            'user_id' => auth()->id(),
            'action' => 'Mise a jour stage',
            'description' => "Stage {$stage->theme} modifie",
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage mis a jour.');
    }

>>>>>>> e9635ab
    public function destroy(Stage $stage)
    {
        $theme = $stage->theme;
        $stage->jours()->detach();
        $stage->delete();

        Activity::create([
<<<<<<< HEAD
            'user_id'     => auth()->id(),
            'action'      => 'Suppression stage',
            'description' => "Stage {$theme} supprimé"
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage supprimé.');
    }

    // Services déjà faits
=======
            'user_id' => auth()->id(),
            'action' => 'Suppression stage',
            'description' => "Stage {$theme} supprime",
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage supprime.');
    }

>>>>>>> e9635ab
    public function servicesDisponibles(Etudiant $etudiant)
    {
        $servicesFaits = $etudiant->stages()->pluck('service_id')->toArray();
        $services = Service::whereNotIn('id', $servicesFaits)->get();
<<<<<<< HEAD
        return response()->json($services);
    }

    // Vue badge
=======

        return response()->json($services);
    }

>>>>>>> e9635ab
    public function badge(Stage $stage)
    {
        $stage->load(['etudiant', 'service', 'typestage', 'badge', 'jours']);

        $aujourdHui = now();
<<<<<<< HEAD
        $statutEnCours = $stage->date_debut > $aujourdHui ? 'À venir' : ($stage->date_fin < $aujourdHui ? 'Terminé' : 'En cours');
=======
        $statutEnCours = $stage->date_debut > $aujourdHui ? 'A venir' : ($stage->date_fin < $aujourdHui ? 'Termine' : 'En cours');
>>>>>>> e9635ab

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

<<<<<<< HEAD
    // Corbeille
    public function trash()
    {
        $stages = Stage::onlyTrashed()->with(['etudiant', 'typestage', 'badge', 'jours'])->paginate(10);
=======
    public function trash()
    {
        $stages = Stage::onlyTrashed()->with(['etudiant', 'typestage', 'badge', 'jours'])->paginate(10);

>>>>>>> e9635ab
        return view('admin.stages.corbeille', compact('stages'));
    }

    public function restore($id)
    {
        $stage = Stage::onlyTrashed()->findOrFail($id);
        $stage->restore();

<<<<<<< HEAD
        return redirect()->route('stages.index')->with('success', 'Stage restauré avec succès 🚀');
=======
        return redirect()->route('stages.index')->with('success', 'Stage restaure avec succes.');
>>>>>>> e9635ab
    }

    public function forceDelete($id)
    {
        $stage = Stage::onlyTrashed()->findOrFail($id);
        $stage->forceDelete();

<<<<<<< HEAD
        return redirect()->route('stages.trash')->with('success', 'Stage supprimé définitivement 🗑️');
=======
        return redirect()->route('stages.trash')->with('success', 'Stage supprime definitivement.');
>>>>>>> e9635ab
    }
}
