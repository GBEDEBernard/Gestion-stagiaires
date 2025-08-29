<?php

namespace App\Http\Controllers;

use App\Models\Jour;
use Illuminate\Http\Request;

class JourController extends Controller
{
    // Liste des jours
    public function index()
    {
        $jours = Jour::all();
        return view('admin.jours.index', compact('jours'));
    }

    // Formulaire de création
    public function create()
    {
        return view('admin.jours.create');
    }

    // Enregistrer un nouveau jour
    public function store(Request $request)
    {
        $request->validate([
            'jour' => 'required|string|max:255|unique:jours,jour',
        ]);

        Jour::create($request->only('jour'));

        return redirect()->route('jours.index')->with('success', 'Jour créé avec succès.');
    }

    // Formulaire d'édition
    public function edit(Jour $jour)
    {
        return view('admin.jours.edit', compact('jour'));
    }

    // Mettre à jour un jour
    public function update(Request $request, Jour $jour)
    {
        $request->validate([
            'jour' => 'required|string|max:255|unique:jours,jour,' . $jour->id,
        ]);

        $jour->update($request->only('jour'));

        return redirect()->route('jours.index')->with('success', 'Jour mis à jour.');
    }

    // Supprimer un jour
    public function destroy(Jour $jour)
    {
        $jour->delete();
        return redirect()->route('jours.index')->with('success', 'Jour supprimé.');
    }
}
