<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Stage;
use Barryvdh\DomPDF\Facade\Pdf;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::paginate(10);
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

   public function edit(Badge $badge) {
    return view('admin.badges.edit', compact('badge'));
}

   public function update(Request $request, Badge $badge)
{
    $validated = $request->validate([
        'badge' => 'required|string|max:255|unique:badges,badge,' . $badge->id,
    ]);

    $badge->update($validated);

    // Optionnel : activité
    Activity::create([
        'user_id' => auth()->id(),
        'action' => 'Mise à jour badge',
        'description' => "Badge {$badge->badge} modifié"
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

    
   

   public function show(Stage $stage)
    {
        $stage->load('etudiant', 'badge', 'service', 'typestage');
        return view('admin.stages.badge', compact('stage'));
    }

    public function download(Stage $stage)
    {
        $stage->load('etudiant', 'badge', 'service', 'typestage');
        $pdf = Pdf::loadView('admin.stages.badge_pdf', compact('stage'))
            ->setPaper([0, 0, 413.3858, 584.252]) // A6 en points
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        return $pdf->download('badge_'.$stage->etudiant->nom.'.pdf');
    }  
}
