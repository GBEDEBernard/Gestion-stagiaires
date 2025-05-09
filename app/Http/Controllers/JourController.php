<?php

namespace App\Http\Controllers;

use App\Models\Jour;
use Illuminate\Http\Request;

class JourController extends Controller
{
    // 🔍 Affiche la liste de toutes les écoles (page d’index)
    public function index()
    {
        $jours = Jour::all(); // Récupère toutes les écoles depuis la base
        return view('admin.jours.index', compact('jours')); // Envoie à la vue index
    }

    // ➕ Affiche le formulaire pour créer une nouvelle école
    public function create()
    {
        return view('admin.jours.create'); // Affiche le formulaire "Ajouter une école"
    }

    // 💾 Enregistre une nouvelle école en base de données
    public function store(Request $request)
    {
        // Valide les données envoyées par le formulaire
        $request->validate([
            'jour' => 'required|string|max:255',
            
        ]);

        // Crée une école avec les données validées
        Jour::create($request->all());

        // Redirige avec un message de succès
        return redirect()->route('jours.index')->with('success', 'jours créée');
    }

    // ✏️ Affiche le formulaire de modification d’une école existante
    public function edit(Jour $jour)
    {
        return view('admin.jours.edit', compact('jour')); // Affiche le formulaire avec les infos de l’école
    }

    // 🔁 Met à jour une école dans la base
    public function update(Request $request, Jour $jour)
    {
        // Valide les données
        $request->validate([
            'jour' => 'required|string|max:255'
        ]);
        

        // Met à jour l’école avec les nouvelles infos
        $jour->update($request->all());

        // Redirige avec message
        return redirect()->route('jours.index')->with('success', 'Jour mise à jour');
    }

    // ❌ Supprime une école
    public function destroy(Jour $jour)
    {
        $jour->delete(); // Supprime dans la base
        return redirect()->route('jours.index')->with('success', 'Jour supprimée');
    }
}