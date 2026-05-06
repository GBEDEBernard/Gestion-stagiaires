<?php

namespace App\Http\Controllers;

use App\Models\Domaine;
use App\Models\Site;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;

class DomaineController extends Controller
{
    // Liste des domaines
        public function index()
    {
        $domaines = Domaine::with('sites')->orderBy('nom')->paginate(10);
        return view('admin.domaines.index', compact('domaines'));
    }

    // Formulaire création
    public function create()
    {
        $sites = Site::orderBy('name')->get();
        return view('admin.domaines.create', compact('sites'));
    }

    // Enregistrer un nouveau domaine
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:domaines,nom',
            'description' => 'nullable|string|max:1000',
            'site_ids' => 'nullable|array',
            'site_ids.*' => 'exists:sites,id',
        ]);

        $domaine = Domaine::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        // Associer aux sites sélectionnés
        if ($request->has('site_ids')) {
            $domaine->sites()->sync($request->site_ids);
        }

        // Log activité
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Création domaine',
            'description' => "Domaine {$domaine->nom} créé"
        ]);

        return redirect()->route('domaines.index')->with('success', 'Domaine créé avec succès.');
    }

    // Formulaire édition
public function edit($id)
{
    $domaine = Domaine::findOrFail($id);
    $sites = Site::orderBy('name')->get();
    $domaine->load('sites', 'users');
    return view('admin.domaines.edit', compact('domaine', 'sites'));
}

// Mettre à jour
public function update(Request $request, $id)
{
    $domaine = Domaine::findOrFail($id);

    $request->validate([
        'nom' => 'required|string|max:255|unique:domaines,nom,' . $domaine->id,
        'description' => 'nullable|string|max:1000',
        'site_ids' => 'nullable|array',
        'site_ids.*' => 'exists:sites,id',
    ]);

    $old_nom = $domaine->nom;
    $domaine->update($request->only(['nom', 'description']));

    if ($request->has('site_ids')) {
        $domaine->sites()->sync($request->site_ids);
    } else {
        $domaine->sites()->detach();
    }

    Activity::create([
        'user_id' => auth()->id(),
        'action' => 'Mise à jour domaine',
        'description' => "Domaine {$old_nom} → {$domaine->nom} modifié"
    ]);

    return redirect()->route('domaines.index')->with('success', 'Domaine mis à jour.');
}

// Supprimer
public function destroy($id)
{
    $domaine = Domaine::findOrFail($id);
    $nom = $domaine->nom;
    $domaine->delete();

    Activity::create([
        'user_id' => auth()->id(),
        'action' => 'Suppression domaine',
        'description' => "Domaine {$nom} supprimé"
    ]);

    return redirect()->route('domaines.index')->with('success', 'Domaine supprimé.');
}

// Afficher les détails
public function show($id)
{
    $domaine = Domaine::findOrFail($id);
    $domaine->loadCount(['users']);
    $domaine->load('sites');

    Activity::create([
        'user_id' => auth()->id(),
        'action' => 'Consultation domaine',
        'description' => "Domaine {$domaine->nom} consulté"
    ]);

    return view('admin.domaines.show', compact('domaine'));
}
}
