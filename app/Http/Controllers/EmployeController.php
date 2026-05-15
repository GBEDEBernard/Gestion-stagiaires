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
    public function index() {
        $employes = Employe::with('personnel.user', 'domaine', 'site')->paginate(10);
        return view('admin.employes.index', compact('employes'));
    }

    public function create() {
        $domaines = Domaine::all();
        $sites = Site::all();
        return view('admin.employes.create', compact('domaines', 'sites'));
    }

    public function store(Request $request) {
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

    public function generateAccount(Employe $employe, AccountGenerationService $service) {
        $personnel = $employe->personnel;
        if ($personnel->user) {
            return back()->with('error', 'Un compte existe déjà.');
        }
        $service->generateForPersonnel($personnel, 'employe');
        return back()->with('success', "Compte généré pour {$personnel->full_name}. Un email a été envoyé.");
    }

    public function edit(Employe $employe) {
        $domaines = Domaine::all();
        $sites = Site::all();
        return view('admin.employes.edit', compact('employe', 'domaines', 'sites'));
    }

    public function update(Request $request, Employe $employe) {
        $personnel = $employe->personnel;
        $data = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => ['required','email', Rule::unique('personnels','email')->ignore($personnel->id)],
            'telephone'      => 'nullable|string|max:20',
            'genre'          => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'domaine_id'     => 'required|exists:domaines,id',
            'site_id'        => 'required|exists:sites,id',
            'poste'          => 'nullable|string|max:255',
            'matricule'      => ['required','string', Rule::unique('employes','matricule')->ignore($employe->id)],
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

    public function destroy(Employe $employe) {
        $employe->personnel()->delete();
        $employe->delete();
        return redirect()->route('employes.index')->with('success', 'Employé supprimé.');
    }

    // Corbeille (soft delete)
    public function trash() {
        $employes = Employe::onlyTrashed()->with('personnel')->paginate(10);
        return view('admin.employes.trash', compact('employes'));
    }

    public function restore($id) {
        $employe = Employe::onlyTrashed()->findOrFail($id);
        $employe->restore();
        $employe->personnel()->restore();
        return redirect()->route('employes.trash')->with('success', 'Employé restauré.');
    }

    public function forceDelete($id) {
        $employe = Employe::onlyTrashed()->findOrFail($id);
        $employe->personnel()->forceDelete();
        $employe->forceDelete();
        return redirect()->route('employes.trash')->with('success', 'Employé définitivement supprimé.');
    }
}