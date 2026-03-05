<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;

class EtudiantController extends Controller
{
    public function index()
    {
        $etudiants = Etudiant::paginate(5);
        return view('admin.etudiants.index', compact('etudiants'));
    }

    public function create()
    {
        return view('admin.etudiants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'genre' => 'required',
            'email' => 'required|email|unique:etudiants,email',
            'telephone' => 'nullable|string|max:20',
            'ecole' => 'nullable|string|max:255',
        ]);

        Etudiant::create($data);
        return redirect()->route('etudiants.index')->with('success', 'Étudiant créé avec succès.');
    }

    public function edit(Etudiant $etudiant)
    {
        return view('admin.etudiants.edit', compact('etudiant'));
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'genre' => 'required',
            'email' => 'required|email|unique:etudiants,email,' . $etudiant->id,
            'telephone' => 'nullable|string|max:20',
            'ecole' => 'nullable|string|max:255',
        ]);

        $etudiant->update($data);
        return redirect()->route('etudiants.index')->with('success', 'Étudiant mis à jour.');
    }

    public function destroy(Etudiant $etudiant)
    {
        $etudiant->delete();
        return redirect()->route('etudiants.index')->with('success', 'Étudiant supprimé.');
    }

    // la méthode des corbeille
    // Méthode corbeille
    public function trash()
    {
        $etudiants = Etudiant::onlyTrashed()->paginate(5); // nom correct
        return view('admin.etudiants.corbeille', compact('etudiants'));
    }

    // restaurer la suppression
    public function restore($id)
    {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->restore();

        return redirect()->route('etudiants.index')->with('success', 'Étudiant restauré avec succès 🚀');
    }

    // suppression définitive
    public function forceDelete($id)
    {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->forceDelete();

        return redirect()->route('etudiants.trash')->with('success', 'Étudiant supprimé définitivement 🗑️');
    }
}
