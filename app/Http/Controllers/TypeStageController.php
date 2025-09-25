<?php

namespace App\Http\Controllers;

use App\Models\TypeStage;
use Illuminate\Http\Request;

class TypeStageController extends Controller
{
    // Afficher la liste des types de stages
    public function index()
    {
        $type_stages = TypeStage::all();
        return view('admin.type_stages.index', compact('type_stages'));
    }

    // Afficher le formulaire pour créer un nouveau type de stage
    public function create()
    {
        return view('admin.type_stages.create');
    }

    // Sauvegarder un nouveau type de stage
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:typestages,libelle',
            'code'    => 'required|string|max:10|unique:typestages,code',
        ]);

        TypeStage::create([
            'libelle' => $request->libelle,
            'code'    => $request->code,
        ]);

        return redirect()->route('type_stages.index')
                         ->with('success', 'Type de stage créé avec succès !');
    }

    // Afficher le formulaire d'édition pour un type de stage existant
    public function edit(TypeStage $type_stages)
    {
        return view('admin.type_stages.edit', ['typeStage' => $type_stages]);
    }

    // Mettre à jour un type de stage existant
    public function update(Request $request, TypeStage $type_stages)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:typestages,libelle,'.$type_stages->id,
            'code'    => 'required|string|max:10|unique:typestages,code,'.$type_stages->id,
        ]);

        $type_stages->update([
            'libelle' => $request->libelle,
            'code'    => $request->code,
        ]);

        return redirect()->route('type_stages.index')
                         ->with('success', 'Type de stage mis à jour avec succès !');
    }

    // Supprimer un type de stage
    public function destroy(TypeStage $type_stages)
    {
        $type_stages->delete();
        return redirect()->route('type_stages.index')
                         ->with('success', 'Type de stage supprimé avec succès !');
    }
}
