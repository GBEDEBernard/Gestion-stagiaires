<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\Jour;
use App\Models\TypeStage;
use App\Models\Badge;
use Illuminate\Http\Request;

class StagiaireController extends Controller
{
    // Afficher la liste des stagiaires
    public function index()
    {
        $stagiaires = Stagiaire::with(['jours', 'typestage', 'badge'])->paginate(10); // ou 15, 20…
        return view('admin.stagiaires.index', compact('stagiaires'));
    }
    

    // Formulaire de création
    public function create()
{
    $jours = Jour::all();
    $typestages = TypeStage::all();
    $badges = Badge::all();

    return view('admin.stagiaires.create', compact('jours', 'typestages', 'badges'));
}


    // Stocker un nouveau stagiaire
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:stagiaires',
            'telephone' => 'required|unique:stagiaires',
// les stockage du jour 
            'jours_id' => 'required|array',
            'jours_id.*' => 'exists:jours,id',

           'typestage_id' => 'required|exists:typestages,id',
            'badge_id' => 'required|exists:badges,id',
            'ecole' => 'nullable|string',
            'theme' => 'nullable|string',
            'date_debut' => 'required|date',
           'date_fin' => 'required|date|after_or_equal:date_debut'

            
        ]);

        $stagiaire = Stagiaire::create($request->except('jours_id'));

        // Association many-to-many
        $stagiaire->jours()->attach($request->jours_id);
        // Stagiaire::create($request->all());
        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès');
    }

    // Formulaire de modification
    public function edit(Stagiaire $stagiaire)
    {
        $jours = Jour::all();
        $typestages = TypeStage::all();
        $badges = Badge::all();
        return view('admin.stagiaires.edit', compact('stagiaire', 'jours', 'typestages', 'badges'));
    }

    // Mettre à jour un stagiaire
    public function update(Request $request, Stagiaire $stagiaire)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:stagiaires,email,' . $stagiaire->id,
            'telephone' => 'required|unique:stagiaires,telephone,' . $stagiaire->id,
              // autres validations...
            'jours_id' => 'required|array',
            'jours_id.*' => 'exists:jours,id',

            'typestages_id' => 'required|exists:typestages,id',
            'badges_id' => 'required|exists:badges,id',
            'ecole' => 'nullable|string',
            'theme' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut'

        ]);
    //   $stagiaire = Stagiaire::update($request->except('jours_id'));

      // Association many-to-many
      $stagiaire->update($request->except('jours_id'));

      $stagiaire->jours()->sync($request->jours_id);
        // $stagiaire->update($request->all());

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire mis à jour avec succès');
    }

    // Supprimer un stagiaire
    public function destroy(Stagiaire $stagiaire)
    {
        $stagiaire->delete();
        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire supprimé avec succès');
    }
}
