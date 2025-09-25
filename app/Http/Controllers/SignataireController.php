<?php

namespace App\Http\Controllers;

use App\Models\Signataire;
use Illuminate\Http\Request;

class SignataireController extends Controller
{
    /**
     * Afficher la liste des signataires
     */
    public function index()
    {
        $signataires = Signataire::orderBy('ordre')->get();
        return view('admin.signataires.index', compact('signataires'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.signataires.create');
    }

    /**
     * Enregistrer un nouveau signataire
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:255',
            'poste' => 'required|string|max:255',
            'sigle' => 'required|string|max:10',
            'ordre' => 'nullable|integer|min:1'
        ]);

        Signataire::create($validated);

        return redirect()->route('signataires.index')
                         ->with('success', 'Signataire ajouté avec succès.');
    }

    /**
     * Afficher le formulaire d’édition
     */
    public function edit(Signataire $signataire)
    {
        return view('admin.signataires.edit', compact('signataire'));
    }

    /**
     * Mettre à jour un signataire existant
     */
    public function update(Request $request, Signataire $signataire)
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:255',
            'poste' => 'required|string|max:255',
            'sigle' => 'required|string|max:10',
            'ordre' => 'nullable|integer|min:1'
        ]);

        $signataire->update($validated);

        return redirect()->route('signataires.index')
                         ->with('success', 'Signataire modifié avec succès.');
    }

    /**
     * Supprimer un signataire
     */
    public function destroy(Signataire $signataire)
    {
        $signataire->delete();

        return redirect()->route('signataires.index')
                         ->with('success', 'Signataire supprimé avec succès.');
    }
}
