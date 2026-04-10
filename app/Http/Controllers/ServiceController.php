<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Activity;

class ServiceController extends Controller
{
    // Liste des services
    public function index()
    {
        $services = Service::paginate(10);
        return view('admin.services.index', compact('services'));
    }

    // Formulaire création
    public function create()
    {
        return view('admin.services.create');
    }

    // Enregistrer un nouveau service
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:services,nom',
        ]);

        $service = Service::create($request->only('nom'));

        // Log activité
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Création service',
            'description' => "Service {$service->nom} créé"
        ]);

        return redirect()->route('services.index')->with('success', 'Service créé avec succès.');
    }

    // Formulaire édition
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    // Mettre à jour
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:services,nom,' . $service->id,
        ]);

        $service->update($request->only('nom'));

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Mise à jour service',
            'description' => "Service {$service->nom} modifié"
        ]);

        return redirect()->route('services.index')->with('success', 'Service mis à jour.');
    }

    // Supprimer
    public function destroy(Service $service)
    {
        $nom = $service->nom;
        $service->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression service',
            'description' => "Service {$nom} supprimé"
        ]);

        return redirect()->route('services.index')->with('success', 'Service supprimé.');
    }
}
