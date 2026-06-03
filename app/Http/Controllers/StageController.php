<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Badge;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Jour;
use App\Models\Signataire;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StageController extends Controller
{
    public function index(Request $request)
    {
        $query = Stage::with(['etudiant', 'etudiant.personnel', 'typestage', 'domaine', 'service', 'site', 'supervisor', 'badge', 'jours']);

        // Filtre par statut
        if ($request->filled('statut')) {
            if ($request->statut === 'En cours') {
                $query->whereDate('date_debut', '<=', now())
                    ->whereDate('date_fin', '>=', now());
            } elseif (in_array($request->statut, ['Termine', 'Terminé'], true)) {
                $query->whereDate('date_fin', '<', now());
            } elseif (in_array($request->statut, ['A venir', 'À venir'], true)) {
                $query->whereDate('date_debut', '>', now());
            }
        }

        // Filtre par type de stage
        if ($request->filled('typestage')) {
            $query->where('typestage_id', $request->typestage);
        }

        // Filtre par nom étudiant
        if ($request->filled('nom')) {
            $query->whereHas('etudiant.personnel', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->nom . '%')
                    ->orWhere('prenom', 'like', '%' . $request->nom . '%');
            });
        }

        // Filtre par école
        if ($request->filled('ecole')) {
            $query->whereHas('etudiant', function ($q) use ($request) {
                $q->where('ecole', 'like', '%' . $request->ecole . '%');
            });
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
        $domaines = Domaine::all();
        $sites = Site::where('is_active', true)
            ->where(function ($query) {
                $query->where('code', 'like', 'TFG%')
                    ->orWhere('name', 'like', '%TFG%');
            })
            ->orderBy('name')
            ->get();

        if ($sites->isEmpty()) {
            $sites = Site::where('is_active', true)->orderBy('name')->get();
        }

        $supervisors = User::role(['admin', 'superviseur'])
            ->join('personnels', 'personnels.id', '=', 'users.personnel_id')
            ->orderBy('personnels.nom')
            ->orderBy('personnels.prenom')
            ->select('users.*')
            ->get();

        $jours = Jour::all();

        // Récupération du flag modal et de l'étudiant pré-sélectionné.
        // On accepte soit la session flash (redirigée depuis la création de personnel),
        // soit les query params `show_modal` / `etudiant_id` pour la compatibilité.
        $showModalFromSession = session()->pull('show_modal', false);
        $showModal = $showModalFromSession || request()->boolean('show_modal');

        $preselectedEtudiantId = session()->pull('preselected_etudiant_id', null) ?? request()->query('etudiant_id');

        // Diagnostic log pour vérifier la présence des flags (utile en debug local)
        Log::debug('StageController.create modal flags', [
            'session_show_modal' => $showModalFromSession,
            'request_show_modal' => request()->query('show_modal'),
            'computed_showModal' => $showModal,
            'session_preselected' => session()->get('preselected_etudiant_id'),
            'preselectedEtudiantId' => $preselectedEtudiantId,
            'query_etudiant_id' => request()->query('etudiant_id'),
        ]);

        return view('admin.stages.create', compact(
            'etudiants',
            'typestages',
            'domaines',
            'sites',
            'supervisors',
            'badges',
            'jours',
            'showModal',
            'preselectedEtudiantId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id'  => 'required|exists:etudiants,id',
            'typestage_id' => 'nullable|exists:typestages,id',
            'domaine_id'   => 'nullable|exists:domaines,id',
            'site_id'      => 'nullable|exists:sites,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'badge_id'     => 'nullable|exists:badges,id',
            'theme'        => 'nullable|string|max:255',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'jours_id'     => 'required|array',
            'jours_id.*'   => 'exists:jours,id',
        ]);

        if ($request->filled('site_id')) {
            $site = Site::find($request->site_id);
            if ($site && !Str::startsWith($site->code, 'TFG') && !Str::contains($site->name, 'TFG')) {
                return back()
                    ->withErrors(['site_id' => 'Les stagiaires doivent être rattachés à un site TFG.'])
                    ->withInput()
                    ->with('open_stage_modal', true)
                    ->with('new_etudiant_id', $request->etudiant_id)
                    ->with('new_etudiant_nom', optional(Etudiant::find($request->etudiant_id))->personnel?->full_name);
            }
        }

        $etudiant  = Etudiant::findOrFail($request->etudiant_id);
        $badge     = $request->filled('badge_id') ? Badge::findOrFail($request->badge_id) : null;
        $dateDebut = $request->date_debut;
        $dateFin   = $request->date_fin;

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
            return back()
                ->withErrors([
                    'etudiant_id' => "Cet etudiant a deja un stage qui chevauche cette periode.",
                ])
                ->withInput()
                ->with('open_stage_modal', true)
                ->with('new_etudiant_id', $request->etudiant_id)
                ->with('new_etudiant_nom', optional($etudiant->personnel)->full_name);
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

        $stage = Stage::create($request->only([
            'etudiant_id',
            'typestage_id',
            'domaine_id',
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
        ]));

        $stage->jours()->sync($request->jours_id);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Creation stage',
            'description' => "Stage {$stage->theme} ajoute pour l'etudiant {$stage->etudiant->nom}",
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage cree avec succes.');
    }

    public function show(Stage $stage)
    {
        $stage->load(['etudiant', 'typestage', 'badge', 'jours', 'domaine', 'service', 'site', 'supervisor']);

        $statutEnCours = $stage->date_debut && $stage->date_fin
            ? (now()->between($stage->date_debut, $stage->date_fin) ? 'En cours' : (now()->gt($stage->date_fin) ? 'Termine' : 'A venir'))
            : 'A venir';

        $signataires = Signataire::orderBy('ordre')->get();
        $eligibleUsers = User::role('admin')
            ->where('is_signer', true)
            ->permission('signer_attestation')
            ->with(['personnel.personnable'])
            ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
            ->select('users.*')
            ->orderBy('personnels.nom')
            ->orderBy('personnels.prenom')
            ->get();

        $selectedSignataireIds = [];
        if ($stage->attestation) {
            $selectedSignataireIds = $stage->attestation->signataires()
                ->pluck('user_id')
                ->filter()
                ->all();
        }

        $etudiant    = $stage->etudiant;
        $nombreStages = $etudiant->stages()->count();

        $stagesTermines = $etudiant->stages()
            ->where('id', '!=', $stage->id)
            ->where('date_fin', '<', now())
            ->with(['typestage', 'domaine'])
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
            'eligibleUsers',
            'selectedSignataireIds',
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
        $domaines = Domaine::all();
        $sites = Site::where('is_active', true)
            ->where(function ($query) {
                $query->where('code', 'like', 'TFG%')
                    ->orWhere('name', 'like', '%TFG%');
            })
            ->orderBy('name')
            ->get();

        if ($sites->isEmpty()) {
            $sites = Site::where('is_active', true)->orderBy('name')->get();
        }
        $supervisors = User::role(['admin', 'superviseur'])
            ->join('personnels', 'users.personnel_id', '=', 'personnels.id')
            ->orderBy('personnels.nom')
            ->orderBy('personnels.prenom')
            ->select('users.*')
            ->get();

        $badges = Badge::all();
        $jours = Jour::all();
        $selectedJours = $stage->jours->pluck('id')->toArray();

        return view('admin.stages.edit', compact(
            'stage',
            'etudiants',
            'typestages',
            'domaines',
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
            'etudiant_id'  => 'required|exists:etudiants,id',
            'typestage_id' => 'nullable|exists:typestages,id',
            'domaine_id'   => 'nullable|exists:domaines,id',
            'site_id'      => 'nullable|exists:sites,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'badge_id'     => 'nullable|exists:badges,id',
            'theme'        => 'nullable|string|max:255',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'jours_id'     => 'required|array',
            'jours_id.*'   => 'exists:jours,id',
        ]);

        $etudiant  = Etudiant::findOrFail($request->etudiant_id);
        $badge     = $request->filled('badge_id') ? Badge::findOrFail($request->badge_id) : null;
        $dateDebut = $request->date_debut;
        $dateFin   = $request->date_fin;

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
            'domaine_id',
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
        ]));

        $stage->jours()->sync($request->jours_id ?? []);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Mise a jour stage',
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
            'user_id'     => auth()->id(),
            'action'      => 'Suppression stage',
            'description' => "Stage {$theme} supprime",
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage supprime.');
    }

    public function domainesDisponibles(Etudiant $etudiant)
    {
        $domainesFaits = $etudiant->stages()->pluck('domaine_id')->toArray();
        $domaines = Domaine::whereNotIn('id', $domainesFaits)->get();

        return response()->json($domaines);
    }

    public function badge(Stage $stage)
    {
        $stage->load(['etudiant', 'service', 'typestage', 'badge', 'jours']);

        $aujourdHui    = now();
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
