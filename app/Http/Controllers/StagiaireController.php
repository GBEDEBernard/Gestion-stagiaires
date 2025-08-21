<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\TypeStage;
use App\Models\Badge;
use App\Models\Jour;
use Illuminate\Http\Request;

class StagiaireController extends Controller
{
    /**
     * Affiche la liste paginée des stagiaires avec leurs relations.
     */
    public function index()
    {
        // Récupère les stagiaires avec type de stage, badge et jours
        $stagiaires = Stagiaire::with(['typestage', 'badge', 'jours'])
            ->latest() // ordre décroissant par date de création
            ->paginate(10); // pagination 10 par page

        // Retourne la vue index avec les stagiaires
        return view('admin.stagiaires.index', compact('stagiaires'));
    }

    /**
     * Formulaire de création d’un nouveau stagiaire.
     */
    public function create()
    {
        $typestages = TypeStage::all(); // tous les types de stage
        $badges = Badge::all();         // tous les badges
        $jours = Jour::all();           // tous les jours possibles

        // Retourne la vue create avec toutes les listes nécessaires
        return view('admin.stagiaires.create', compact('typestages', 'badges', 'jours'));
    }

    /**
     * Enregistre un nouveau stagiaire en base de données.
     */
    public function store(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'email'        => 'required|email|unique:stagiaires,email',
            'telephone'    => 'nullable|string|max:20',
            'typestage_id' => 'required|exists:typestages,id',
            'badge_id'     => 'required|exists:badges,id',
            'ecole'        => 'nullable|string|max:150',
            'theme'        => 'nullable|string|max:200',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'jours_id'     => 'array', // tableau d'IDs de jours
        ]);

        // Création du stagiaire
        $stagiaire = Stagiaire::create($request->only([
            'nom', 'prenom', 'email', 'telephone',
            'typestage_id', 'badge_id', 'ecole', 'theme',
            'date_debut', 'date_fin'
        ]));

        // Association des jours sélectionnés via relation N-N
        $stagiaire->jours()->sync($request->jours_id ?? []);

        // Redirection vers la liste avec message de succès
        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès.');
    }

    /**
     * Affiche le profil détaillé d’un stagiaire.
     */
    public function show(Stagiaire $stagiaire)
    {
        // Charge les relations nécessaires
        $stagiaire->load(['typestage', 'badge', 'jours']);

        // Vérifie si le stage est en cours
        $statutEnCours = now()->between($stagiaire->date_debut, $stagiaire->date_fin);

        return view('admin.stagiaires.show', compact('stagiaire', 'statutEnCours'));
    }

    /**
     * Formulaire d’édition d’un stagiaire existant.
     */
    public function edit(Stagiaire $stagiaire)
    {
        $typeStages = TypeStage::all(); // liste des types de stage
        $badges = Badge::all();         // liste des badges
        $jours = Jour::all();           // liste de tous les jours

        // Charge les relations pour pré-remplir le formulaire
        $stagiaire->load('jours', 'typestage', 'badge');

        return view('admin.stagiaires.edit', compact('stagiaire', 'typeStages', 'badges', 'jours'));
    }

    /**
     * Met à jour un stagiaire existant en base de données.
     */
    public function update(Request $request, Stagiaire $stagiaire)
    {
        // Validation des champs du formulaire
        $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'email'        => 'required|email|unique:stagiaires,email,' . $stagiaire->id,
            'telephone'    => 'nullable|string|max:20',
            'typestage_id' => 'required|exists:typestages,id',
            'badge_id'     => 'required|exists:badges,id',
            'ecole'        => 'nullable|string|max:150',
            'theme'        => 'nullable|string|max:200',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after_or_equal:date_debut',
            'jours_id'     => 'array',
        ]);

        // Mise à jour des informations du stagiaire
        $stagiaire->update($request->only([
            'nom', 'prenom', 'email', 'telephone',
            'typestage_id', 'badge_id', 'ecole', 'theme',
            'date_debut', 'date_fin'
        ]));

        // Mise à jour de la relation N-N avec les jours
        $stagiaire->jours()->sync($request->jours_id ?? []);

        // Redirection vers le profil du stagiaire avec message de succès
        return redirect()->route('stagiaires.show', $stagiaire->id)
                         ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Supprime un stagiaire de la base de données.
     */
    public function destroy(Stagiaire $stagiaire)
    {
        $stagiaire->delete();

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire supprimé.');
    }


   /**
 * Affiche le badge d’un stagiaire pour visualisation et impression.
 */
public function badge(Stagiaire $stagiaire)
{
    $stagiaire->load(['typestage', 'badge']); // charger relations nécessaires

    $entreprise = [
        'nom' => 'TECHNOLOGY ROREVER GROUP',
        'abreviation' => 'TFG'
    ];

    return view('admin.stagiaires.badge', compact('stagiaire', 'entreprise'));
}

}
