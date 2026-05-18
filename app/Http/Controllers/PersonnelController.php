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
    public function index()
    {
        $personnels = Personnel::with('personnable', 'user')->paginate(15);
        return view('admin.personnels.index', compact('personnels'));
    }

    public function create()
    {
        $sites = Site::with('domaines')->get();
        $domainesParSite = [];
        foreach ($sites as $site) {
            $domainesParSite[$site->id] = $site->domaines->pluck('nom', 'id')->all();
        }

        $now = now();
        $typestages = TypeStage::all();
        $services = Service::all();
        $badges = Badge::whereDoesntHave('stages', function ($query) use ($now) {
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
            $typeRules = [
                'ecole' => 'required|string|max:255',
            ];
        } else {
            $typeRules = [
                'domaine_id' => 'required|exists:domaines,id',
                'site_id'    => 'required|exists:sites,id',
                'poste'      => 'nullable|string|max:255',
                'matricule'  => 'required|string|max:255|unique:employes,matricule',
            ];
        }

        $data = array_merge($data, $request->validate($typeRules));

        if ($data['type'] === 'etudiant') {
            $personnable = Etudiant::create([
                'ecole' => $data['ecole'],
            ]);
        } else {
            $personnable = Employe::create([
                'domaine_id' => $data['domaine_id'],
                'site_id'    => $data['site_id'],
                'poste'      => $data['poste'],
                'matricule'  => $data['matricule'],
            ]);
        }

        Personnel::create([
            'nom'              => $data['nom'],
            'prenom'           => $data['prenom'],
            'email'            => $data['email'],
            'telephone'        => $data['telephone'],
            'genre'            => $data['genre'],
            'date_naissance'   => $data['date_naissance'],
            'adresse'          => $data['adresse'],
            'personnable_type' => get_class($personnable),
            'personnable_id'   => $personnable->id,
            'created_by'       => auth()->id(),
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

    public function show(Personnel $personnel)
    {
        $personnel->load('personnable', 'user');
        return view('admin.personnels.show', compact('personnel'));
    }

    public function edit(Personnel $personnel)
    {
        $personnel->load('personnable');
        $domaines = Domaine::all();
        $sites = Site::all();
        $type = $personnel->personnable_type === Employe::class ? 'employe' : 'etudiant';
        return view('admin.personnels.edit', compact('personnel', 'domaines', 'sites', 'type'));
    }

    public function update(Request $request, Personnel $personnel)
    {
        $type = $personnel->personnable_type === Employe::class ? 'employe' : 'etudiant';

        $baseRules = [
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('personnels', 'email')->ignore($personnel->id)],
            'telephone'      => 'nullable|string|max:20',
            'genre'          => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'adresse'        => 'nullable|string|max:1000',
            'type'           => ['required', Rule::in(['etudiant', 'employe'])],
        ];

        $data = $request->validate($baseRules);

        if ($data['type'] === 'etudiant') {
            $typeRules = [
                'ecole' => 'required|string|max:255',
            ];
        } else {
            $typeRules = [
                'domaine_id' => 'required|exists:domaines,id',
                'site_id'    => 'required|exists:sites,id',
                'poste'      => 'nullable|string|max:255',
                'matricule'  => ['required', 'string', 'max:255', Rule::unique('employes', 'matricule')->ignore($personnel->personnable_id)],
            ];
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

        if ($type === 'etudiant') {
            $personnel->personnable->update([
                'ecole' => $data['ecole'],
            ]);
        } else {
            $personnel->personnable->update([
                'domaine_id' => $data['domaine_id'],
                'site_id'    => $data['site_id'],
                'poste'      => $data['poste'],
                'matricule'  => $data['matricule'],
            ]);
        }

        return redirect()->route('personnels.index')->with('success', 'Personnel mis à jour.');
    }

    public function destroy(Personnel $personnel)
    {
        if ($personnel->personnable) {
            $personnel->personnable->delete();
        }
        $personnel->delete();

        return redirect()->route('personnels.index')->with('success', 'Personnel supprimé.');
    }

    public function generateAccount(Request $request, Personnel $personnel, AccountGenerationService $service)
    {
        if ($personnel->user) {
            return back()->with('error', 'Un compte existe déjà.');
        }

        $customPassword = $request->input('custom_password');
        $role = $personnel->personnable_type === Employe::class ? 'employe' : 'etudiant';
        $service->generateForPersonnel($personnel, $role, $customPassword);

        return back()->with('success', "Compte généré pour {$personnel->full_name}.");
    }
}
