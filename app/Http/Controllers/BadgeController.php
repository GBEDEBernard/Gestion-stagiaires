<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Activity;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::all();
        return view('admin.badges.index', compact('badges'));
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge' => 'required|string|max:255|unique:badges,badge',
        ]);

        $badge = Badge::create($validated);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Création badge',
            'description' => "Badge {$badge->badge} créé"
        ]);

        return redirect()->route('badges.index')
                         ->with('success', 'Badge créé avec succès.');
    }

    public function edit(Badge $badges)
    {
        return view('admin.badges.edit', compact('badges'));
    }

    public function update(Request $request, Badge $badges)
    {
        $validated = $request->validate([
            'badge' => 'required|string|max:255|unique:badges,badge,' . $badges->id,
        ]);

        $badges->update($validated);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Mise à jour badge',
            'description' => "Badge {$badges->badge} modifié"
        ]);

        return redirect()->route('badges.index')
                         ->with('success', 'Badge mis à jour avec succès.');
    }

    public function destroy(Badge $badges)
    {
        $nom = $badges->badge;
        $badges->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression badge',
            'description' => "Badge {$nom} supprimé"
        ]);

        return redirect()->route('badges.index')
                         ->with('success', 'Badge supprimé avec succès.');
    }
}
