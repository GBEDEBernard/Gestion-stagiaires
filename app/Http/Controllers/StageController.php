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
use Mpdf\Mpdf;

class StageController extends Controller
{
    public function index(Request $request)
    {
        $query = Stage::with(['etudiant', 'etudiant.personnel', 'typestage', 'domaine', 'site', 'supervisor', 'badge', 'jours']);

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

        if ($request->filled('typestage')) {
            $query->where('typestage_id', $request->typestage);
        }

        if ($request->filled('nom')) {
            $query->whereHas('etudiant.personnel', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->nom . '%')
                    ->orWhere('prenom', 'like', '%' . $request->nom . '%');
            });
        }

        if ($request->filled('ecole')) {
            $query->whereHas('etudiant', function ($q) use ($request) {
                $q->where('ecole', 'like', '%' . $request->ecole . '%');
            });
        }

        $now = now();
        $currentYear = $now->month >= 9 ? $now->year . '-' . ($now->year + 1) : ($now->year - 1) . '-' . $now->year;

        if ($request->annee_academique === 'all') {
            $stages = $query->orderBy('annee_academique', 'desc')->orderBy('date_debut', 'desc')->get();
            $stagesParAnnee = $stages->groupBy('annee_academique');
        } else {
            $anneeCible = $request->filled('annee_academique') ? $request->annee_academique : $currentYear;
            $query->where('annee_academique', $anneeCible);
            $stages = $query->orderBy('date_debut', 'desc')->paginate(5)->withQueryString();
            $stagesParAnnee = null;
        }

        $typestages = TypeStage::all();
        $anneesAcademiques = Stage::distinct()->whereNotNull('annee_academique')->orderBy('annee_academique', 'desc')->pluck('annee_academique');

        return view('admin.stages.index', compact('stagesParAnnee', 'typestages', 'stages', 'anneesAcademiques'));
    }

    public function create()
    {
        $now = now();

        $etudiants = Etudiant::whereDoesntHave('stages', function ($query) use ($now) {
            $query->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        // Badges disponibles : ceux qui ne sont utilisés dans AUCUN stage actif ou futur
        $badges = Badge::whereDoesntHave('stages', function ($query) {
            $query->where('date_fin', '>=', now());
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

        $showModalFromSession = session()->pull('show_modal', false);
        $showModal = $showModalFromSession || request()->boolean('show_modal');
        $preselectedEtudiantId = session()->pull('preselected_etudiant_id', null) ?? request()->query('etudiant_id');

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
        $badge     = null;

        if ($request->filled('badge_id')) {
            $badge = Badge::findOrFail($request->badge_id);
        } else {
            $badge = Badge::firstOrCreate(['badge' => Badge::getNextBadgeNumber()]);
            $request->merge(['badge_id' => $badge->id]);
        }

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
                })
                ->where('date_fin', '>=', now())
                ->exists();

            if ($conflictBadge) {
                return back()->withErrors([
                    'badge_id' => 'Ce badge est déjà attribué à un autre stage actif ou futur pour ces dates.',
                ])->withInput();
            }
        }

        $stage = Stage::create(array_merge($request->only([
            'etudiant_id',
            'typestage_id',
            'domaine_id',
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
        ]), ['duree_mois' => $request->nombre_mois]));

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
        $stage->load(['etudiant', 'typestage', 'badge', 'jours', 'domaine', 'site', 'supervisor']);

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

        // Badges disponibles : ceux non utilisés dans un stage actif/futur,
        // plus le badge actuel du stage (pour qu'il reste sélectionné)
        $badges = Badge::where(function ($query) use ($stage) {
            $query->whereDoesntHave('stages', function ($q) {
                $q->where('date_fin', '>=', now());
            })->orWhereHas('stages', function ($q) use ($stage) {
                $q->where('id', $stage->badge_id);
            });
        })->get();

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
                })
                ->where('date_fin', '>=', now())
                ->exists();

            if ($conflictBadge) {
                return back()->withErrors([
                    'badge_id' => 'Ce badge est déjà attribué à un autre stage actif ou futur pour ces dates.',
                ])->withInput();
            }
        }

        $stage->update(array_merge($request->only([
            'etudiant_id',
            'typestage_id',
            'domaine_id',
            'site_id',
            'supervisor_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin',
        ]), ['duree_mois' => $request->nombre_mois]));

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
        $stage->load(['etudiant', 'typestage', 'badge', 'jours']);
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


/**
 * Formulaire d'édition des informations de la fiche de poste.
 */
public function editFichePoste(Stage $stage)
{
    $stage->load(['etudiant.personnel', 'typestage', 'domaine', 'site', 'supervisor.personnel']);

    $typestages = TypeStage::all();
    $domaines   = Domaine::all();

    $defaultLivrables = ['Rapport de stage à déposer', 'Soutenance orale devant jury'];

    return view('admin.stages.fiche_poste_edit', compact('stage', 'typestages', 'domaines', 'defaultLivrables'));
}

/**
 * Enregistre les informations de la fiche de poste puis redirige vers l'aperçu.
 */
public function updateFichePoste(Request $request, Stage $stage)
{
    $request->validate([
        'intitule_poste'    => 'nullable|string|max:255',
        'typestage_id'      => 'nullable|exists:typestages,id',
        'ecole'             => 'nullable|string|max:255',
        'filiere'           => 'nullable|string|max:255',
        'niveau_etude'      => 'nullable|string|max:255',
        'domaine_id'        => 'nullable|exists:domaines,id',
        'tuteur_academique' => 'nullable|string|max:255',
        'theme'             => 'nullable|string|max:255',
        'indemnite'         => 'nullable|string|max:255',
        'livrables'         => 'nullable|array',
        'livrables.*'       => 'string|max:255',
    ]);

    $stage->update([
        'intitule_poste'    => $request->filled('intitule_poste') ? trim($request->intitule_poste) : null,
        'typestage_id'      => $request->filled('typestage_id') ? $request->typestage_id : null,
        'domaine_id'        => $request->filled('domaine_id') ? $request->domaine_id : null,
        'filiere'           => $request->filled('filiere') ? trim($request->filiere) : null,
        'niveau_etude'      => $request->filled('niveau_etude') ? trim($request->niveau_etude) : null,
        'tuteur_academique' => $request->filled('tuteur_academique') ? trim($request->tuteur_academique) : null,
        'indemnite'         => $request->filled('indemnite') ? trim($request->indemnite) : null,
        'livrables'         => $request->filled('livrables') ? array_values($request->livrables) : [],
    ]);

    if ($request->filled('ecole') && $stage->etudiant) {
        $stage->etudiant->update(['ecole' => trim($request->ecole)]);
    }

    if ($request->filled('theme')) {
        $stage->update(['theme' => trim($request->theme)]);
    }

    Activity::create([
        'user_id'     => auth()->id(),
        'action'      => 'Mise a jour fiche de poste',
        'description' => "Fiche de poste mise a jour pour le stage {$stage->id}",
    ]);

    return redirect()->route('stages.fiche-poste.preview', $stage)
        ->with('success', 'Fiche de poste enregistrée. La fiche est maintenant complète.');
}

/**
 * Affiche l'aperçu de la fiche de poste dans l'application
 */
public function previewFichePoste(Stage $stage)
{
    return view('admin.stages.fiche_poste_preview', compact('stage'));
}

/**
 * Télécharge la fiche de poste en PDF
 *
 * Stratégie identique à l'attestation :
 * - Mpdf gère les marges (15 mm sur chaque côté, répétées sur chaque page)
 * - le blade ne rajoute pas de padding quand $isPdf est défini
 */
public function downloadFichePoste(Stage $stage)
{
    $stage->load([
        'etudiant.personnel',
        'supervisor.personnel',
        'domaine',
        'site',
    ]);

    $logoPath    = public_path('images/TFGLOGO.png');
    $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    $isPdf       = true;

    $html = view('admin.stages.fiche_poste_pdf', compact('stage', 'logoDataUri', 'isPdf'))->render();

    $mpdf = new Mpdf([
        'format'        => 'A4',
        'margin_left'   => 15,
        'margin_right'  => 15,
        'margin_top'    => 15,
        'margin_bottom' => 8,
        'default_font'  => 'dejavusans',
    ]);

    ini_set('pcre.backtrack_limit', '4000000');
    ini_set('pcre.jit', '0');

    $mpdf->WriteHTML($html);

    $fileName   = 'fiche_poste_' . $stage->id . '.pdf';
    $pdfContent = $mpdf->Output($fileName, \Mpdf\Output\Destination::STRING_RETURN);

    return response($pdfContent, 200, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
}
}
