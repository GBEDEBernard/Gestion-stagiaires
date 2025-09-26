<?php

namespace App\Http\Controllers;

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

    // Filtre statut
    if ($request->filled('statut')) {
        if ($request->statut == 'En cours') {
            $query->whereDate('date_debut', '<=', now())
                  ->whereDate('date_fin', '>=', now());
        } elseif ($request->statut == 'Terminé') {
            $query->whereDate('date_fin', '<', now());
        } elseif ($request->statut == 'À venir') {
            $query->whereDate('date_debut', '>', now());
        }
    }

    // Filtre type de stage
    if ($request->filled('typestage')) {
        $query->where('typestage_id', $request->typestage);
    }

    $stages = $query->paginate(10)->withQueryString();

    $typestages = TypeStage::all(); // pour le select dans le formulaire

    return view('admin.stages.index', compact('stages', 'typestages'));
}


    // Formulaire de création
    public function create()
    {
        $now = now();

        // Étudiants dispo = pas en stage en cours
        $etudiants = Etudiant::whereDoesntHave('stages', function($q) use ($now) {
            $q->where('date_debut', '<=', $now)
              ->where('date_fin', '>=', $now);
        })->get();

        // Badges dispo = pas attachés à un stage en cours
        $badges = Badge::whereDoesntHave('stages', function($q) use ($now) {
            $q->where('date_debut', '<=', $now)
              ->where('date_fin', '>=', $now);
        })->get();

        $typestages = TypeStage::all();
        $services   = Service::all();
        $jours      = Jour::all();

        return view('admin.stages.create', compact('etudiants','typestages','services','badges','jours'));
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

        $now      = now();
        $etudiant = Etudiant::findOrFail($request->etudiant_id);
        $badge    = Badge::findOrFail($request->badge_id);

        // Vérif étudiant déjà en stage
        $stageEnCours = $etudiant->stages()
            ->where('date_debut', '<=', $now)
            ->where('date_fin', '>=', $now)
            ->exists();

        if ($stageEnCours) {
            return back()->withErrors(['etudiant_id' => 'Cet étudiant est déjà en stage actuellement.'])->withInput();
        }

        // Vérif badge déjà utilisé
        $badgePris = $badge->stages()
            ->where('date_debut', '<=', $now)
            ->where('date_fin', '>=', $now)
            ->exists();

        if ($badgePris) {
            return back()->withErrors(['badge_id' => 'Ce badge est déjà utilisé.'])->withInput();
        }

        // Création du stage
        $stage = Stage::create($request->only([
            'etudiant_id', 'typestage_id', 'service_id', 'badge_id',
            'theme', 'date_debut', 'date_fin'
        ]));

        // Attacher les jours
        $stage->jours()->sync($request->jours_id);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Création stage',
            'description' => "Stage {$stage->theme} ajouté pour l'étudiant {$stage->etudiant->nom}"
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage créé avec succès.');
    }

    // Formulaire d’édition
    public function edit(Stage $stage)
    {
        $etudiants = Etudiant::all();
        $typestages = TypeStage::all();
        $services   = Service::all();
        $badges     = Badge::all();
        $jours      = Jour::all();

        // Récupérer les jours attachés pour pré-cocher
        $selectedJours = $stage->jours->pluck('id')->toArray();

        return view('admin.stages.edit', compact(
            'stage','etudiants','typestages','services','badges','jours','selectedJours'
        ));
    }

    // Mise à jour
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

        $stage->update($request->only([
            'etudiant_id','typestage_id','service_id','badge_id',
            'theme','date_debut','date_fin'
        ]));

        // Mise à jour des jours
        $stage->jours()->sync($request->jours_id ?? []);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Mise à jour stage',
            'description' => "Stage {$stage->theme} modifié"
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage mis à jour.');
    }
    //les service déjà fait et les renvoie

        public function servicesDisponibles(Etudiant $etudiant)
        {
            // IDs des services déjà faits par l'étudiant
            $servicesFaits = $etudiant->stages()->pluck('service_id')->toArray();

            // Services non encore faits
            $services = Service::whereNotIn('id', $servicesFaits)->get();

            return response()->json($services);
        }

    // Supprimer
    public function destroy(Stage $stage)
    {
        $theme = $stage->theme;
        $stage->jours()->detach();
        $stage->delete();

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Suppression stage',
            'description' => "Stage {$theme} supprimé"
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage supprimé.');
    }

    // Vue show 

        public function show(Stage $stage)
        {
            $stage->load(['typestage', 'badge', 'jours']);

            if ($stage->date_debut && $stage->date_fin) {
                $statutEnCours = now()->between($stage->date_debut, $stage->date_fin) ? 'En cours' : 'Terminé';
            } else {
                $statutEnCours = 'À venir';
            }

            // ⚡ Récupérer tous les signataires pour le modal
            $signataires = Signataire::orderBy('ordre')->get();

            return view('admin.stages.show', compact('stage', 'statutEnCours', 'signataires'));
        }

    // Vue badge
   public function badge($id)
{
    $stage = Stage::with(['etudiant','service','typestage','badge','jours'])->findOrFail($id);

    // Déterminer le statut
    $aujourdHui = now();
    if ($stage->date_debut > $aujourdHui) {
        $statutEnCours = 'À venir';
    } elseif ($stage->date_fin < $aujourdHui) {
        $statutEnCours = 'Terminé';
    } else {
        $statutEnCours = 'En cours';
    }

    return view('admin.stages.badge', compact('stage','statutEnCours'));
}
    public function site()
    {
        return response(
            QrCode::size(200)
                ->color(0, 0, 0)          // QR blanc
                ->backgroundColor(255, 255, 255)  // bleu foncé
                ->generate('https://www.tfgbusiness.com'),
            200,
            ['Content-Type' => 'image/svg+xml']
        );
    }

    // la méthode des corbeille
  public function trash()
{
    $stages = Stage::onlyTrashed()->with(['etudiant','typestage','badge','jours'])->paginate(10);
    return view('admin.stages.corbeille', compact('stages'));
}

// restaurer la supression
public function restore($id)
{
    $stage = Stage::onlyTrashed()->findOrFail($id);
    $stage->restore();

    return redirect()->route('stages.index')->with('success', 'Stage restauré avec succès 🚀');
}
// supression definitive
public function forceDelete($id)
{
    $stage = Stage::onlyTrashed()->findOrFail($id);
    $stage->forceDelete();

    return redirect()->route('stages.trash')->with('success', 'Stage supprimé définitivement 🗑️');
}

}
