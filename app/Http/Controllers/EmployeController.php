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
    // =========================================================================
    // INDEX
    // =========================================================================
    public function index(Request $request)
    {
        $query = Employe::with('personnel.user', 'domaine', 'site');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('personnel', function ($q2) use ($search) {
                      $q2->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($domaineId = $request->get('domaine_id')) {
            $query->where('domaine_id', $domaineId);
        }

        if ($siteId = $request->get('site_id')) {
            $query->where('site_id', $siteId);
        }

        $accountStatus = $request->get('account_status');
        if ($accountStatus === 'with') {
            $query->whereHas('personnel.user');
        } elseif ($accountStatus === 'without') {
            $query->whereDoesntHave('personnel.user');
        }

        $employes = $query->latest('id')->paginate(10)->withQueryString();
        $domaines = Domaine::orderBy('nom')->get();
        $sites    = Site::orderBy('name')->get();

        return view('admin.employes.index', compact('employes', 'domaines', 'sites'));
    }

    // =========================================================================
    // CREATE
    // =========================================================================
    public function create()
    {
        $domaines = Domaine::all();
        $sites    = Site::all();

        return view('admin.employes.create', compact('domaines', 'sites'));
    }

    // =========================================================================
    // STORE — corrigé : Personnel créé en premier, FK personnel_id renseignée
    // =========================================================================
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
        ]);

        // 1. Personnel d'abord
        $personnel = Personnel::create([
            'nom'            => $data['nom'],
            'prenom'         => $data['prenom'],
            'email'          => $data['email'],
            'telephone'      => $data['telephone'],
            'genre'          => $data['genre'],
            'date_naissance' => $data['date_naissance'],
            'created_by'     => auth()->id(),
        ]);

        // 2. Employé avec FK
        $employe = Employe::create([
            'personnel_id' => $personnel->id,
            'domaine_id'   => $data['domaine_id'],
            'site_id'      => $data['site_id'],
            'poste'        => $data['poste'],
        ]);

        // 3. Polymorphisme sur personnel
        $personnel->update([
            'personnable_type' => Employe::class,
            'personnable_id'   => $employe->id,
        ]);

        return redirect()->route('employes.index')->with('success', 'Employé créé avec succès.');
    }

    // =========================================================================
    // SHOW
    // =========================================================================
    public function show(Employe $employe)
    {
        $employe->load('personnel.user', 'domaine', 'site');
        return view('admin.employes.show', compact('employe'));
    }

    // =========================================================================
    // GENERATE ACCOUNT
    // =========================================================================
    public function generateAccount(Request $request, Employe $employe, AccountGenerationService $service)
    {
        $personnel = $employe->personnel;
        if (!$personnel) {
            return back()->with('error', 'Aucun personnel associé à cet employé.');
        }
        if ($personnel->user) {
            $service->resendProvisioningEmail($personnel);

            if (!$service->lastProvisioningEmailSent()) {
                return back()->with('error', "Un compte existe déjà pour {$personnel->full_name}, mais l'email d'activation n'a pas pu être envoyé. Vérifiez la configuration SMTP.");
            }

            return back()->with('success', "Un compte existe déjà pour {$personnel->full_name}. L'email d'activation a été renvoyé.");
        }
        $service->generateForPersonnel($personnel, 'employe', $request->input('custom_password'));

        if (!$service->lastProvisioningEmailSent()) {
            return back()->with('error', "Compte généré pour {$personnel->full_name}, mais l'email d'activation n'a pas pu être envoyé. Vérifiez la configuration SMTP.");
        }

        return back()->with('success', "Compte généré pour {$personnel->full_name}. Un email a été envoyé.");
    }

    // =========================================================================
    // EDIT
    // =========================================================================
    public function edit(Employe $employe)
    {
        $domaines = Domaine::all();
        $sites    = Site::all();
        return view('admin.employes.edit', compact('employe', 'domaines', 'sites'));
    }

    // =========================================================================
    // UPDATE
    // =========================================================================
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
        ]);

        if ($personnel->user) {
            $personnel->user->update([
                'domaine_id' => $data['domaine_id'],
            ]);
        }

        return redirect()->route('employes.index')->with('success', 'Employé mis à jour.');
    }

    // =========================================================================
    // DESTROY (soft delete)
    // =========================================================================
    public function destroy(Employe $employe)
    {
        if ($employe->personnel) {
            if ($employe->personnel->user) {
                $employe->personnel->user->delete();
            }
            $employe->personnel->delete();
        }
        $employe->delete();

        return redirect()->route('employes.index')
            ->with('success', 'Employé déplacé dans la corbeille.');
    }

    // =========================================================================
    // CORBEILLE
    // =========================================================================
    public function trash()
    {
        $employes = Employe::onlyTrashed()->with(['personnel' => fn($q) => $q->withTrashed()])->paginate(10);
        return view('admin.employes.trash', compact('employes'));
    }

    public function restore($id)
    {
        $employe = Employe::onlyTrashed()->with(['personnel' => fn($q) => $q->withTrashed()])->findOrFail($id);
        $employe->restore();
        if ($employe->personnel?->trashed()) {
            $employe->personnel->restore();
        }
        return redirect()->route('employes.trash')->with('success', 'Employé restauré.');
    }

    public function forceDelete($id)
    {
        $employe = Employe::onlyTrashed()
            ->with(['personnel' => fn($q) => $q->withTrashed()->with('user')])
            ->findOrFail($id);

        $personnel = $employe->personnel;
        $employe->forceDelete();

        if ($personnel) {
            if ($personnel->user) {
                $personnel->user->forceDelete();
            }
            $personnel->forceDelete();
        }

        return redirect()->route('employes.trash')->with('success', 'Employé définitivement supprimé.');
    }
}
