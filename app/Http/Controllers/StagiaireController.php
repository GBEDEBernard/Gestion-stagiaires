<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\TypeStage;
use App\Models\Badge;
use App\Models\Jour;
use App\Models\Activity;
use Illuminate\Http\Request;

class StagiaireController extends Controller
{
    public function index()
    {
        $stagiaires = Stagiaire::with(['typestage', 'badge', 'jours'])
            ->latest()->paginate(10);

        return view('admin.stagiaires.index', compact('stagiaires'));
    }

    public function create()
    {
        $typestages = TypeStage::all();
        $badges = Badge::all();
        $jours = Jour::all();

        return view('admin.stagiaires.create', compact('typestages', 'badges', 'jours'));
    }

    public function store(Request $request)
    {
       $request->validate([
                    'nom'          => 'required|string|max:100',
                    'prenom'       => 'required|string|max:100',
                    'email'        => 'required|email|unique:stagiaires,email',
                    'telephone'    => 'nullable|string|max:20',
                    'typestage_id' => 'required|exists:typestages,id',
                    'badge_id'     => [
                        'required',
                        'exists:badges,id',
                        function ($attribute, $value, $fail) use ($request) {
                            $exists = \App\Models\Stagiaire::where('badge_id', $value)
                                ->where('date_debut', '<=', $request->date_fin)
                                ->where('date_fin', '>=', $request->date_debut)
                                ->exists();

                            if ($exists) {
                                $fail("Ce badge est déjà attribué à un autre stagiaire sur la même période.");
                            }
                        }
                    ],
                    'ecole'        => 'nullable|string|max:150',
                    'theme'        => 'nullable|string|max:200',
                    'date_debut'   => 'required|date',
                    'date_fin'     => 'required|date|after_or_equal:date_debut',
                    'jours_id'     => 'array',
                ]);


        $stagiaire = Stagiaire::create($request->only([
            'nom', 'prenom', 'email', 'telephone',
            'typestage_id', 'badge_id', 'ecole', 'theme',
            'date_debut', 'date_fin'
        ]));

        $stagiaire->jours()->sync($request->jours_id ?? []);

        // Log activité
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Création stagiaire',
            'description' => "Stagiaire {$stagiaire->nom} {$stagiaire->prenom} ajouté"
        ]);

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès.');
    }

    public function show(Stagiaire $stagiaire)
    {
        $stagiaire->load(['typestage', 'badge', 'jours']);
        $statutEnCours = now()->between($stagiaire->date_debut, $stagiaire->date_fin);

        return view('admin.stagiaires.show', compact('stagiaire', 'statutEnCours'));
    }

    public function edit(Stagiaire $stagiaire)
    {
        $typeStages = TypeStage::all();
        $badges = Badge::all();
        $jours = Jour::all();

        $stagiaire->load('jours', 'typestage', 'badge');

        return view('admin.stagiaires.edit', compact('stagiaire', 'typeStages', 'badges', 'jours'));
    }

    public function update(Request $request, Stagiaire $stagiaire)
    {
        $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'email'        => 'required|email|unique:stagiaires,email,' . $stagiaire->id,
            'telephone'    => 'nullable|string|max:20',
            'typestage_id' => 'required|exists:typestages,id',
                'badge_id' => [
                    'required',
                    'exists:badges,id',
                    function ($attribute, $value, $fail) use ($request, $stagiaire) {
                        $exists = \App\Models\Stagiaire::where('badge_id', $value)
                            ->where('id', '!=', $stagiaire->id)
                            ->where('date_debut', '<=', $request->date_fin)
                            ->where('date_fin', '>=', $request->date_debut)
                            ->exists();

                        if ($exists) {
                            $fail("Ce badge est déjà attribué à un autre stagiaire sur la même période.");
                        }
                    }
                ],
            'ecole'        => 'nullable|string|max:150',
            'theme'        => 'nullable|string|max:200',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'jours_id'     => 'array',
        ]);

        $stagiaire->update($request->only([
            'nom', 'prenom', 'email', 'telephone',
            'typestage_id', 'badge_id', 'ecole', 'theme',
            'date_debut', 'date_fin'
        ]));

        $stagiaire->jours()->sync($request->jours_id ?? []);

        // Log activité
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Mise à jour stagiaire',
            'description' => "Profil de {$stagiaire->nom} {$stagiaire->prenom} modifié"
        ]);

        return redirect()->route('stagiaires.show', $stagiaire->id)
                         ->with('success', 'Profil mis à jour avec succès.');
    }

    public function destroy(Stagiaire $stagiaire)
    {
        $nom = $stagiaire->nom;
        $stagiaire->delete();

        // Log activité
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression stagiaire',
            'description' => "Stagiaire {$nom} supprimé"
        ]);

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire supprimé.');
    }

    public function badge(Stagiaire $stagiaire)
    {
        $stagiaire->load(['typestage', 'badge']);

        $entreprise = [
            'nom' => 'TECHNOLOGY ROREVER GROUP',
            'abreviation' => 'TFG'
        ];

        return view('admin.stagiaires.badge', compact('stagiaire', 'entreprise'));
    }
}
