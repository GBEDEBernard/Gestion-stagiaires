<?php
// app/Http/Controllers/EmployeController.php
namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Personnel;
use App\Models\Domaine;
use App\Models\Site;
use App\Services\AccountGenerationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeController extends Controller
{
   // app/Http/Controllers/EmployeController.php

public function index(Request $request)
{
    $query = Employe::with('personnel.user', 'domaine', 'site');

    // Recherche textuelle
    if ($search = $request->get('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('matricule', 'like', "%{$search}%")
              ->orWhereHas('personnel', function ($q2) use ($search) {
                  $q2->where('nom', 'like', "%{$search}%")
                     ->orWhere('prenom', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    // Filtre par domaine
    if ($domaineId = $request->get('domaine_id')) {
        $query->where('domaine_id', $domaineId);
    }

    // Filtre par site
    if ($siteId = $request->get('site_id')) {
        $query->where('site_id', $siteId);
    }

    // Filtre par statut du compte
    $accountStatus = $request->get('account_status');
    if ($accountStatus === 'with') {
        $query->whereHas('personnel.user');
    } elseif ($accountStatus === 'without') {
        $query->whereDoesntHave('personnel.user');
    }

    $employes = $query->latest('id')->paginate(10)->withQueryString();

    // Pour les listes déroulantes des filtres
    $domaines = Domaine::orderBy('nom')->get();
    $sites = Site::orderBy('name')->get();

    return view('admin.employes.index', compact('employes', 'domaines', 'sites'));
}

    public function create()
    {
        $domaines = Domaine::all();
        $sites = Site::all();
        return view('admin.employes.create', compact('domaines', 'sites'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => 'required|email|unique:personnels,email',
            'telephone'      => 'nullable|string|max:20',
            'genre'          => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'domaine_id'     => 'required|exists:domaines,id',
            'site_id'        => 'required|exists:sites,id',
            'poste'          => 'nullable|string|max:255',
            'matricule'      => 'required|string|unique:employes,matricule',
        ]);

        $employe = Employe::create([
            'domaine_id' => $data['domaine_id'],
            'site_id'    => $data['site_id'],
            'poste'      => $data['poste'],
            'matricule'  => $data['matricule'],
        ]);

        Personnel::create([
            'nom'              => $data['nom'],
            'prenom'           => $data['prenom'],
            'email'            => $data['email'],
            'telephone'        => $data['telephone'],
            'genre'            => $data['genre'],
            'date_naissance'   => $data['date_naissance'],
            'personnable_type' => Employe::class,
            'personnable_id'   => $employe->id,
            'created_by'       => auth()->id(),
        ]);

        return redirect()->route('employes.index')->with('success', 'Employé créé sans compte.');
    }
    // public function syncAccount(Employe $employe)
    public function show(Employe $employe)
    {
        $employe->load('personnel.user', 'domaine', 'site');
        return view('admin.employes.show', compact('employe'));
    }

    public function generateAccount(Request $request, Employe $employe, AccountGenerationService $service)
    {
        $personnel = $employe->personnel;
        if ($personnel->user) {
            return back()->with('error', 'Un compte existe déjà.');
        }
        $customPassword = $request->input('custom_password');
        $service->generateForPersonnel($personnel, 'employe', $customPassword);
        return back()->with('success', "Compte généré pour {$personnel->full_name}. Un email a été envoyé.");
    }

    public function edit(Employe $employe)
    {
        $domaines = Domaine::all();
        $sites = Site::all();
        return view('admin.employes.edit', compact('employe', 'domaines', 'sites'));
    }

    public function update(Request $request, Employe $employe)
    {
        $personnel = $employe->personnel;
        $data = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('personnels', 'email')->ignore($personnel->id)],
            'telephone'      => 'nullable|string|max:20',
            'genre'          => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'domaine_id'     => 'required|exists:domaines,id',
            'site_id'        => 'required|exists:sites,id',
            'poste'          => 'nullable|string|max:255',
            'matricule'      => ['required', 'string', Rule::unique('employes', 'matricule')->ignore($employe->id)],
        ]);

        $personnel->update([
            'nom'            => $data['nom'],
            'prenom'         => $data['prenom'],
            'email'          => $data['email'],
            'telephone'      => $data['telephone'],
            'genre'          => $data['genre'],
            'date_naissance' => $data['date_naissance'],
        ]);

        $employe->update([
            'domaine_id' => $data['domaine_id'],
            'site_id'    => $data['site_id'],
            'poste'      => $data['poste'],
            'matricule'  => $data['matricule'],
        ]);

        return redirect()->route('employes.index')->with('success', 'Employé mis à jour.');
    }

      public function destroy(Employe $employe)
    {
        // 🔥 On supprime le personnel parent (cela déclenche la suppression en cascade du user et de l'employé)
        if ($employe->personnel) {
            $employe->personnel->delete();
        } else {
            $employe->delete(); // fallback si la relation est absente
        }
        return redirect()->route('employes.index')
            ->with('success', 'Employé déplacé dans la corbeille.');
    }
   

    // Corbeille (soft delete)
    public function trash()
    {
        $employes = Employe::onlyTrashed()->with(['personnel' => function ($query) {
            $query->withTrashed();
        }])->paginate(10);
        return view('admin.employes.trash', compact('employes'));
    }

    public function restore($id)
    {
        $employe = Employe::onlyTrashed()->with(['personnel' => function ($query) {
            $query->withTrashed();
        }])->findOrFail($id);
        $employe->restore();
        $personnel = $employe->personnel;
        if ($personnel && method_exists($personnel, 'restore')) {
            $personnel->restore();
        }
        return redirect()->route('employes.trash')->with('success', 'Employé restauré.');
    }

    public function forceDelete($id)
    {
        $employe = Employe::onlyTrashed()->with(['personnel' => function ($q) {
            $q->withTrashed()->with('user');
        }])->findOrFail($id);

        // Supprimer d'abord la fiche employé (enfant), puis l'utilisateur et enfin le personnel.
        $employe->forceDelete();

        $personnel = $employe->personnel;
        if ($personnel) {
            if ($personnel->user) {
                $personnel->user()->forceDelete();
            }
            $personnel->forceDelete();
        }

        return redirect()->route('employes.trash')->with('success', 'Employé définitivement supprimé.');
    }
    public function refresh()
    {
        \Artisan::call('cache:clear');
        return redirect()->route('employes.index')->with('success', 'Cache vidé');
    }
}
