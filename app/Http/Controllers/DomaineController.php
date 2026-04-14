<?php

namespace App\Http\Controllers;

use App\Models\Domaine;
use Illuminate\Http\Request;
use App\Models\Activity;

class DomaineController extends Controller
{
    // Liste des domaines
    public function index()
    {
        $domaines = Domaine::paginate(10);
        return view('admin.domaines.index', compact('domaines'));
    }

    // Formulaire création
    public function create()
    {
        return view('admin.domaines.create');
    }

    // Enregistrer un nouveau domaine
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:domaines,nom',
            'description' => 'nullable|string|max:1000',
        ]);

        $domaine = Domaine::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        // Log activité
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Création domaine',
            'description' => "Domaine {$domaine->nom} créé"
        ]);

        return redirect()->route('domaines.index')->with('success', 'Domaine créé avec succès.');
    }

    // Formulaire édition
    public function edit(Domaine $domaine)
    {
        return view('admin.domaines.edit', compact('domaine'));
    }

    // Mettre à jour
    public function update(Request $request, Domaine $domaine)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:domaines,nom,' . $domaine->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $domaine->update($request->only(['nom', 'description']));

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Mise à jour domaine',
            'description' => "Domaine {$domaine->nom} modifié"
        ]);

        return redirect()->route('domaines.index')->with('success', 'Domaine mis à jour.');
    }

    // Supprimer
    public function destroy(Domaine $domaine)
    {
        $nom = $domaine->nom;
        $domaine->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression domaine',
            'description' => "Domaine {$nom} supprimé"
        ]);

        return redirect()->route('domaines.index')->with('success', 'Domaine supprimé.');
    }
}
