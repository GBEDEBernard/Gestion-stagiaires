<?php

namespace App\Http\Controllers;

use App\Models\Ecole;
use App\Models\Etudiant;
use Illuminate\Http\Request;

class EcoleController extends Controller
{
    /**
     * Liste paginée des écoles de provenance, avec recherche.
     */
    public function index(Request $request)
    {
        $query = Ecole::query();

        if ($q = trim((string) $request->get('q'))) {
            $query->where('nom', 'like', '%' . $q . '%');
        }

        $ecoles = $query->orderBy('nom')->paginate(10)->withQueryString();

        // Nombre d'étudiants rattachés à chaque école.
        $etudiantsCount = Etudiant::whereNotNull('ecole')
            ->selectRaw('ecole, count(*) as total')
            ->groupBy('ecole')
            ->pluck('total', 'ecole');

        return view('admin.ecoles.index', compact('ecoles', 'etudiantsCount'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('admin.ecoles.create');
    }

    /**
     * Enregistre une nouvelle école.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255|unique:ecoles,nom',
        ]);

        Ecole::create($data);

        return redirect()->route('ecoles.index')
            ->with('success', 'École ajoutée avec succès !');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Ecole $ecole)
    {
        return view('admin.ecoles.edit', ['ecole' => $ecole]);
    }

    /**
     * Met à jour une école existante.
     */
    public function update(Request $request, Ecole $ecole)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255|unique:ecoles,nom,' . $ecole->id,
        ]);

        $ecole->update($data);

        return redirect()->route('ecoles.index')
            ->with('success', 'École mise à jour avec succès !');
    }

    /**
     * Supprime une école (suppression douce).
     */
    public function destroy(Ecole $ecole)
    {
        $ecole->delete();

        return redirect()->route('ecoles.index')
            ->with('success', 'École supprimée avec succès !');
    }
}