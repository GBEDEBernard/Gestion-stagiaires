<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Afficher la liste des badges.
     */
    public function index()
    {
        $badges = Badge::all();
        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Stocker un nouveau niveau.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge' => 'required|string|max:255|unique:badges,badge',
        ]);
        

        Badge::create($validated);
        return redirect()->route('badges.index')
                         ->with('success', 'Badge créé avec succès.');
    }

    /**
     * Afficher le formulaire d’édition.
     */
    public function edit(Badge $badges)
    {
        return view('admin.badges.edit', compact('badges'));
    }

    /**
     * Mettre à jour un niveau existant.
     */
    public function update(Request $request, Badge $badges)
    {
        $validated = $request->validate([
            'badge' => 'required|string|max:255|unique:badges,badge,' . $badges->id,
        ]);
        
        $badges->update($validated);
        return redirect()->route('badges.index')
                         ->with('success', 'Badge mis à jour avec succès.');
    }

    /**
     * Supprimer un niveau.
     */
    public function destroy(Badge $badges)
    {
        $badges->delete();
        return redirect()->route('badges.index')
                         ->with('success', 'Badge supprimé avec succès.');
    }
}
