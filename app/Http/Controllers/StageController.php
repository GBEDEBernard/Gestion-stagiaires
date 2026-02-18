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

        if ($request->filled('typestage')) {
            $query->where('typestage_id', $request->typestage);
        }

        $stages = $query->paginate(5)->withQueryString();
        $typestages = TypeStage::all();

        return view('admin.stages.index', compact('stages', 'typestages'));
    }

    // Formulaire de création des stagiaire
    public function create()
    {
        $now = now();

        $etudiants = Etudiant::whereDoesntHave('stages', function ($q) use ($now) {
            $q->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        $badges = Badge::whereDoesntHave('stages', function ($q) use ($now) {
            $q->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        $typestages = TypeStage::all();
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
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictEtudiant) {
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

        $stage = Stage::create($request->only([
            'etudiant_id',
            'typestage_id',
            'service_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin'
        ]));

        $stage->jours()->sync($request->jours_id);

        Activity::create([
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

        return view('admin.stages.show', compact('stage', 'statutEnCours', 'signataires'));
    }

    // Formulaire d'édition avec model binding
    public function edit(Stage $stage)
    {
        $etudiants = Etudiant::all();
        $typestages = TypeStage::all();
        $services   = Service::all();
        $badges     = Badge::all();
        $jours      = Jour::all();
        $selectedJours = $stage->jours->pluck('id')->toArray();

        return view('admin.stages.edit', compact(
            'stage',
            'etudiants',
            'typestages',
            'services',
            'badges',
            'jours',
            'selectedJours'
        ));
    }

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
                            ->where('date_fin', '>=', $dateFin);
                    });
            })->exists();

        if ($conflictEtudiant) {
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
        }

        $stage->update($request->only([
            'etudiant_id',
            'typestage_id',
            'service_id',
            'badge_id',
            'theme',
            'date_debut',
            'date_fin'
        ]));

        $stage->jours()->sync($request->jours_id ?? []);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Mise à jour stage',
            'description' => "Stage {$stage->theme} modifié"
        ]);

        return redirect()->route('stages.index')->with('success', 'Stage mis à jour.');
    }

    // Supprimer avec model binding
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

    // Services déjà faits
    public function servicesDisponibles(Etudiant $etudiant)
    {
        $servicesFaits = $etudiant->stages()->pluck('service_id')->toArray();
        $services = Service::whereNotIn('id', $servicesFaits)->get();
        return response()->json($services);
    }

    // Vue badge
    public function badge(Stage $stage)
    {
        $stage->load(['etudiant', 'service', 'typestage', 'badge', 'jours']);

        $aujourdHui = now();
        $statutEnCours = $stage->date_debut > $aujourdHui ? 'À venir' : ($stage->date_fin < $aujourdHui ? 'Terminé' : 'En cours');

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

    // Corbeille
    public function trash()
    {
        $stages = Stage::onlyTrashed()->with(['etudiant', 'typestage', 'badge', 'jours'])->paginate(10);
        return view('admin.stages.corbeille', compact('stages'));
    }

    public function restore($id)
    {
        $stage = Stage::onlyTrashed()->findOrFail($id);
        $stage->restore();

        return redirect()->route('stages.index')->with('success', 'Stage restauré avec succès 🚀');
    }

    public function forceDelete($id)
    {
        $stage = Stage::onlyTrashed()->findOrFail($id);
        $stage->forceDelete();

        return redirect()->route('stages.trash')->with('success', 'Stage supprimé définitivement 🗑️');
    }
}
