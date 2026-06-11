<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Domaine;
use App\Models\Employe;
use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\Site;
use App\Models\TypeStage;
use App\Models\User;
use App\Models\Jour;
use App\Services\AccountGenerationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search');
        $type    = $request->get('type');
        $account = $request->get('account');
        $school  = $request->get('school');

        $query = Personnel::with('personnable', 'user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('prenom', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($type && $type !== 'all') {
            if ($type === 'etudiant') {
                $query->where('personnable_type', Etudiant::class);
            } elseif ($type === 'employe') {
                $query->where('personnable_type', Employe::class);
            } elseif ($type === 'inconnu') {
                $query->whereNull('personnable_type');
            }
        }

        if ($school) {
            if ($type && $type !== 'all' && $type !== 'etudiant') {
                $query->whereRaw('0 = 1');
            } else {
                if (!$type || $type === 'all') {
                    $query->where('personnable_type', Etudiant::class);
                }
                $query->whereExists(function ($subquery) use ($school) {
                    $subquery->select('id')
                        ->from('etudiants')
                        ->whereColumn('etudiants.id', 'personnels.personnable_id')
                        ->where('etudiants.ecole', $school);
                });
            }
        }

        if ($account === 'with') {
            $query->whereHas('user');
        } elseif ($account === 'without') {
            $query->whereDoesntHave('user');
        }

        $personnels = $query->paginate(10)->withQueryString();
        $schools    = Etudiant::whereNotNull('ecole')->distinct()->pluck('ecole')->sort()->values();

        return view('admin.personnels.index', compact('personnels', 'schools'));
    }

    public function create()
    {
        $sites = Site::with('domaines')->get();
        $domainesParSite = [];
        foreach ($sites as $site) {
            $domainesParSite[$site->id] = $site->domaines->pluck('nom', 'id')->all();
        }

        $now        = now();
        $typestages = TypeStage::all();
        $services   = Service::all();
        $badges     = Badge::whereDoesntHave('stages', function ($query) use ($now) {
            $query->where('date_debut', '<=', $now)
                ->where('date_fin', '>=', $now);
        })->get();

        $stageSites = Site::where('is_active', true)
            ->where(function ($query) {
                $query->where('code', 'like', 'TFG%')
                    ->orWhere('name', 'like', '%TFG%');
            })
            ->orderBy('name')
            ->get();

        if ($stageSites->isEmpty()) {
            $stageSites = Site::where('is_active', true)->orderBy('name')->get();
        }

        $supervisors = User::role(['admin', 'superviseur'])
            ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
            ->with('personnel')
            ->orderBy('personnels.nom')
            ->orderBy('personnels.prenom')
            ->select('users.*')
            ->get();

        $jours = Jour::all();

        return view('admin.personnels.create', compact(
            'sites',
            'domainesParSite',
            'typestages',
            'services',
            'badges',
            'stageSites',
            'supervisors',
            'jours'
        ));
    }

    public function store(Request $request)
    {
        $baseRules = [
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => 'required|email|unique:personnels,email',
            'telephone'      => 'nullable|string|max:20',
            'genre'          => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'adresse'        => 'nullable|string|max:1000',
            'type'           => 'required|in:etudiant,employe',
        ];

        $data = $request->validate($baseRules);

        if ($data['type'] === 'etudiant') {
            $typeRules = ['ecole' => 'required|string|max:255'];
        } else {
            // Suppression de 'matricule'
            $typeRules = [
                'domaine_id' => 'required|exists:domaines,id',
                'site_id'    => 'required|exists:sites,id',
                'poste'      => 'nullable|string|max:255',
            ];
        }

        $data = array_merge($data, $request->validate($typeRules));

        // Étape 1 : créer le Personnel
        $personnel = Personnel::create([
            'nom'            => $data['nom'],
            'prenom'         => $data['prenom'],
            'email'          => $data['email'],
            'telephone'      => $data['telephone'],
            'genre'          => $data['genre'],
            'date_naissance' => $data['date_naissance'],
            'adresse'        => $data['adresse'],
            'created_by'     => auth()->id(),
        ]);

        // Étape 2 : créer la fiche métier
        if ($data['type'] === 'etudiant') {
            $personnable = Etudiant::create([
                'personnel_id' => $personnel->id,
                'ecole'        => $data['ecole'],
            ]);
        } else {
            $personnable = Employe::create([
                'personnel_id' => $personnel->id,
                'domaine_id'   => $data['domaine_id'],
                'site_id'      => $data['site_id'],
                'poste'        => $data['poste'],
                // pas de matricule
            ]);
        }

        // Étape 3 : mettre à jour le polymorphisme
        $personnel->update([
            'personnable_type' => get_class($personnable),
            'personnable_id'   => $personnable->id,
        ]);

        if ($data['type'] === 'etudiant') {
            return redirect()->route('personnels.create')
                ->with('open_stage_modal', true)
                ->with('new_etudiant_id', $personnable->id)
                ->with('new_etudiant_nom', trim($data['prenom'] . ' ' . $data['nom']))
                ->with('success', 'Étudiant créé. Complétez maintenant son stage.');
        }

        return redirect()->route('personnels.index')->with('success', 'Personnel créé avec succès.');
    }

    public function update(Request $request, Personnel $personnel)
    {
        $baseRules = [
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('personnels', 'email')->ignore($personnel->id)],
            'telephone'      => 'nullable|string|max:20',
            'genre'          => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'adresse'        => 'nullable|string|max:1000',
            'type'           => ['required', Rule::in(['etudiant', 'employe', 'inconnu'])],
        ];

        $data = $request->validate($baseRules);
        $formType = $data['type'];

        if ($formType === 'etudiant') {
            $typeRules = ['ecole' => 'sometimes|required|string|max:255'];
        } elseif ($formType === 'employe') {
            $typeRules = [
                'domaine_id' => 'sometimes|required|exists:domaines,id',
                'site_id'    => 'sometimes|required|exists:sites,id',
                'poste'      => 'nullable|string|max:255',
                'supervisor_id' => 'nullable|exists:users,id',
                // plus de matricule
            ];
        } else {
            $typeRules = [];
        }

        $data = array_merge($data, $request->validate($typeRules));

        $personnel->update([
            'nom'            => $data['nom'],
            'prenom'         => $data['prenom'],
            'email'          => $data['email'],
            'telephone'      => $data['telephone'],
            'genre'          => $data['genre'],
            'date_naissance' => $data['date_naissance'],
            'adresse'        => $data['adresse'],
        ]);

        if ($formType === 'etudiant' && $personnel->personnable) {
            $personnel->personnable->update([
                'ecole' => $data['ecole'] ?? $personnel->personnable->ecole,
            ]);
        } elseif ($formType === 'employe' && $personnel->personnable) {
            $personnel->personnable->update([
                'domaine_id'    => $data['domaine_id'] ?? $personnel->personnable->domaine_id,
                'site_id'       => $data['site_id'] ?? $personnel->personnable->site_id,
                'poste'         => $data['poste'] ?? $personnel->personnable->poste,
                'supervisor_id' => $data['supervisor_id'] ?? null,
                // pas de matricule
            ]);
        }

        return redirect()->route('personnels.index')->with('success', 'Personnel mis à jour.');
    }

    public function show(Personnel $personnel)
    {
        $personnel->load('personnable', 'user');
        return view('admin.personnels.show', compact('personnel'));
    }

   public function edit(Personnel $personnel)
{
    $personnel->load('personnable');
    $domaines = Domaine::all();
    $sites    = Site::all();
    $type = match ($personnel->personnable_type) {
        Employe::class => 'employe',
        Etudiant::class => 'etudiant',
        default => 'inconnu',
    };

    // Construction de la map site → domaines (comme dans create)
    $allSites = Site::with('domaines')->get();
    $domainesParSite = [];
    foreach ($allSites as $site) {
        $domainesParSite[$site->id] = $site->domaines->pluck('nom', 'id')->all();
    }

    $superviseurs = User::role(['admin', 'superviseur'])
        ->with('personnel')
        ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
        ->select('users.*')
        ->orderBy('personnels.nom')
        ->get();

    $supervisorIdValue = ($personnel->personnable instanceof Employe)
        ? $personnel->personnable->supervisor_id
        : null;

    return view('admin.personnels.edit', compact(
        'personnel', 'domaines', 'sites', 'type',
        'superviseurs', 'supervisorIdValue', 'domainesParSite'
    ));
}

    public function trash()
    {
        $personnels = Personnel::onlyTrashed()->with(['personnable' => function ($query) {
            $query->withTrashed();
        }])->paginate(10);
        return view('admin.personnels.trash', compact('personnels'));
    }

    public function destroy(Personnel $personnel)
    {
        if ($personnel->user) {
            $personnel->user->delete();
        }
        if ($personnel->employe) {
            $personnel->employe->delete();
        }
        if ($personnel->etudiant) {
            $personnel->etudiant->delete();
        }
        if ($personnel->personnable) {
            $personnel->personnable->delete();
        }
        $personnel->delete();

        return redirect()->route('personnels.index')
            ->with('success', 'Personnel déplacé dans la corbeille.');
    }

    public function restore($id)
    {
        $personnel = Personnel::onlyTrashed()->findOrFail($id);
        if ($personnel->personnable && $personnel->personnable->trashed()) {
            $personnel->personnable->restore();
        }
        if ($personnel->user && $personnel->user->trashed()) {
            $personnel->user->restore();
        }
        $personnel->restore();

        return redirect()->route('corbeille.index')
            ->with('success', 'Personnel restauré.');
    }

    public function forceDelete($id)
    {
        $personnel = Personnel::onlyTrashed()->findOrFail($id);
        if ($personnel->user) {
            $personnel->user->forceDelete();
        }
        if ($personnel->personnable) {
            $personnel->personnable->forceDelete();
        }
        $personnel->forceDelete();

        return redirect()->route('corbeille.index')
            ->with('success', 'Personnel définitivement supprimé.');
    }

    public function generateAccount(Request $request, Personnel $personnel, AccountGenerationService $service)
    {
        if ($personnel->user) {
            $service->resendProvisioningEmail($personnel);
            if (!$service->lastProvisioningEmailSent()) {
                return back()->with('error', "Un compte existe déjà pour {$personnel->full_name}, mais l'email d'activation n'a pas pu être envoyé. Vérifiez la configuration SMTP.");
            }
            return back()->with('success', "Un compte existe déjà pour {$personnel->full_name}. L'email d'activation a été renvoyé.");
        }

        $customPassword = $request->input('custom_password');
        $role = $personnel->personnable_type === Employe::class ? 'employe' : 'etudiant';
        $service->generateForPersonnel($personnel, $role, $customPassword);

        if (!$service->lastProvisioningEmailSent()) {
            return back()->with('error', "Compte généré pour {$personnel->full_name}, mais l'email d'activation n'a pas pu être envoyé. Vérifiez la configuration SMTP.");
        }
        return back()->with('success', "Compte généré pour {$personnel->full_name}.");
    }
}