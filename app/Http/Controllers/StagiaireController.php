<?php

namespace App\Http\Controllers;
// class StagiaireController extends Controller
// {
//     // Afficher la liste des stagiaires
//     public function index()
//     {
//         $stagiaires = Stagiaire::with(['jours', 'typestage', 'badge'])->paginate(10); // ou 15, 20…
//         return view('admin.stagiaires.index', compact('stagiaires'));
//     }
    

//     // Formulaire de création
//     public function create()
// {
//     $jours = Jour::all();
//     $typestages = TypeStage::all();
//     $badges = Badge::all();

//     return view('admin.stagiaires.create', compact('jours', 'typestages', 'badges'));
// }


//     // Stocker un nouveau stagiaire
//     public function store(Request $request)
//     {
//         $request->validate([
//             'nom' => 'required|string',
//             'prenom' => 'required|string',
//             'email' => 'required|email|unique:stagiaires',
//             'telephone' => 'required|unique:stagiaires',
// // les stockage du jour 
//             'jours_id' => 'required|array',
//             'jours_id.*' => 'exists:jours,id',
//            'typestage_id' => 'required|exists:typestages,id',
//             'badge_id' => 'required|exists:badges,id|unique:stagiaires,badge_id',
//             'ecole' => 'nullable|string',
//             'theme' => 'nullable|string',
//             'date_debut' => 'required|date',
//            'date_fin' => 'required|date|after_or_equal:date_debut'

            
//         ]);

//         $stagiaire = Stagiaire::create($request->except('jours_id'));

//         // Association many-to-many
//         $stagiaire->jours()->attach($request->jours_id);
//         // Stagiaire::create($request->all());
//         return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès');
//     }

//     // Formulaire de modification
//     public function edit(Stagiaire $stagiaire)
//     {
//         $jours = Jour::all();
//         $typestages = TypeStage::all();
//         $badges = Badge::all();
//         return view('admin.stagiaires.edit', compact('stagiaire', 'jours', 'typestages', 'badges'));
//     }

//     // Mettre à jour un stagiaire
//     public function update(Request $request, Stagiaire $stagiaire)
//     {
//         $request->validate([
//             'nom' => 'required|string',
//             'prenom' => 'required|string',
//             'email' => 'required|email|unique:stagiaires,email,' . $stagiaire->id,
//             'telephone' => 'required|unique:stagiaires,telephone,' . $stagiaire->id,
//               // autres validations...
//             'jours_id' => 'required|array',
//             'jours_id.*' => 'exists:jours,id',

//             'typestages_id' => 'required|exists:typestages,id',
//             'badges_id' => 'required|exists:badges,id|unique:stagiaires,badge_id,'.$stagiaire->id,
//             'ecole' => 'nullable|string',
//             'theme' => 'nullable|string',
//             'date_debut' => 'required|date',
//             'date_fin' => 'required|date|after_or_equal:date_debut'

//         ]);
//     //   $stagiaire = Stagiaire::update($request->except('jours_id'));

//       // Association many-to-many
//       $stagiaire->update($request->except('jours_id'));

//       $stagiaire->jours()->sync($request->jours_id);
//         // $stagiaire->update($request->all());

//         return redirect()->route('stagiaires.index')->with('success', 'Stagiaire mis à jour avec succès');
//     }

//     // Supprimer un stagiaire
//     public function destroy(Stagiaire $stagiaire)
//     {
//         $stagiaire->delete();
//         return redirect()->route('stagiaires.index')->with('success', 'Stagiaire supprimé avec succès');
//     }
// }


use App\Models\Stagiaire;
use App\Models\Jour;
use App\Models\TypeStage;
use App\Models\Badge;
use App\Rules\BadgeNotInActiveStage;
use Illuminate\Http\Request;

class StagiaireController extends Controller
{
    // Afficher la liste des stagiaires
    public function index()
    {
        $stagiaires = Stagiaire::with(['jours', 'typestage', 'badge'])->paginate(10);
        return view('admin.stagiaires.index', compact('stagiaires'));
    }

    // Formulaire de création
    public function create()
    {
        $jours = Jour::all();
        $typestages = TypeStage::all();
        $badges = Badge::getAvailableBadges();
        return view('admin.stagiaires.create', compact('jours', 'typestages', 'badges'));
    }

    // Stocker un nouveau stagiaire
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:stagiaires,email',
            'telephone' => 'required|unique:stagiaires,telephone',
            'jours_id' => 'required|array',
            'jours_id.*' => 'exists:jours,id',
            'typestage_id' => 'required|exists:typestages,id',
            'badge_id' => ['required', 'exists:badges,id', new BadgeNotInActiveStage],
            'ecole' => 'nullable|string',
            'theme' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ], [
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est requis.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'jours_id.required' => 'Au moins un jour doit être sélectionné.',
            'jours_id.*.exists' => 'Un ou plusieurs jours sélectionnés sont invalides.',
            'typestage_id.required' => 'Le type de stage est requis.',
            'typestage_id.exists' => 'Le type de stage sélectionné est invalide.',
            'badge_id.required' => 'Le badge est requis.',
            'badge_id.exists' => 'Le badge sélectionné est invalide.',
            'date_debut.required' => 'La date de début est requise.',
            'date_fin.required' => 'La date de fin est requise.',
            'date_fin.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
        ]);

        $stagiaire = Stagiaire::create($request->except('jours_id'));
        $stagiaire->jours()->attach($request->jours_id);

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès');
    }

    // Formulaire de modification
    public function edit(Stagiaire $stagiaire)
    {
        $jours = Jour::all();
        $typestages = TypeStage::all();
        $badges = Badge::getAvailableBadges($stagiaire->id);
        $badges = $badges->merge(Badge::where('id', $stagiaire->badge_id)->get());
        return view('admin.stagiaires.edit', compact('stagiaire', 'jours', 'typestages', 'badges'));
    }
    // Mettre à jour un stagiaire
    public function update(Request $request, Stagiaire $stagiaire)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:stagiaires,email,' . $stagiaire->id,
            'telephone' => 'required|unique:stagiaires,telephone,' . $stagiaire->id,
            'jours_id' => 'required|array',
            'jours_id.*' => 'exists:jours,id',
            'typestage_id' => 'required|exists:typestages,id',
            'badge_id' => ['required', 'exists:badges,id', new BadgeNotInActiveStage($stagiaire->id)],
            'ecole' => 'nullable|string',
            'theme' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ], [
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est requis.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'jours_id.required' => 'Au moins un jour doit être sélectionné.',
            'jours_id.*.exists' => 'Un ou plusieurs jours sélectionnés sont invalides.',
            'typestage_id.required' => 'Le type de stage est requis.',
            'typestage_id.exists' => 'Le type de stage sélectionné est invalide.',
            'badge_id.required' => 'Le badge est requis.',
            'badge_id.exists' => 'Le badge sélectionné est invalide.',
            'date_debut.required' => 'La date de début est requise.',
            'date_fin.required' => 'La date de fin est requise.',
            'date_fin.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
        ]);

        $stagiaire->update($request->except('jours_id'));
        $stagiaire->jours()->sync($request->jours_id);

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire mis à jour avec succès');
    }

    // Supprimer un stagiaire
    public function destroy(Stagiaire $stagiaire)
    {
        $stagiaire->delete();
        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire supprimé avec succès');
    }
}