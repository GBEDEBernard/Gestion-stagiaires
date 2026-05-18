<?php
// app/Http/Controllers/EtudiantController.php
namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Personnel;
use App\Services\AccountGenerationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EtudiantController extends Controller
{
    public function index() {
        $etudiants = Etudiant::with('personnel.user')
        ->latest('id')         // optionnel : les plus récents d'abord
        ->paginate(10);
        return view('admin.etudiants.index', compact('etudiants'));
    }

    public function create() {
        return view('admin.etudiants.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nom'      => 'required|string|max:255',
            'prenom'   => 'required|string|max:255',
            'email'    => 'required|email|unique:personnels,email',
            'telephone'=> 'nullable|string|max:20',
            'genre'    => 'nullable|string|max:50',
            'ecole'    => 'required|string|max:255',
        ]);

        $etudiant = Etudiant::create([
            'ecole'  => $data['ecole'],
        ]);

        Personnel::create([
            'nom'              => $data['nom'],
            'prenom'           => $data['prenom'],
            'email'            => $data['email'],
            'telephone'        => $data['telephone'],
            'genre'            => $data['genre'],
            'personnable_type' => Etudiant::class,
            'personnable_id'   => $etudiant->id,
            'created_by'       => auth()->id(),
        ]);

        return redirect()->route('etudiants.index')->with('success', 'Étudiant créé sans compte. Vous pouvez générer son compte plus tard.');
    }

    // Génération de compte
    public function generateAccount(Request $request, Etudiant $etudiant, AccountGenerationService $service) {
        $personnel = $etudiant->personnel;
        if ($personnel->user) {
            return back()->with('error', 'Un compte existe déjà.');
        }
        $customPassword = $request->input('custom_password');
        $service->generateForPersonnel($personnel, 'etudiant', $customPassword);
        return back()->with('success', "Compte généré pour {$personnel->full_name}. Un email a été envoyé.");
    }
//   public function syncAccount(Etudiant $etudiant) 

    public function edit(Etudiant $etudiant) {
        return view('admin.etudiants.edit', compact('etudiant'));
    }

    public function update(Request $request, Etudiant $etudiant) {
        $personnel = $etudiant->personnel;
        $data = $request->validate([
            'nom'      => 'required|string|max:255',
            'prenom'   => 'required|string|max:255',
            'email'    => ['required','email', Rule::unique('personnels','email')->ignore($personnel->id)],
            'telephone'=> 'nullable|string|max:20',
            'genre'    => 'nullable|string|max:50',
            'ecole'    => 'required|string|max:255',
        ]);

        $personnel->update([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'email'     => $data['email'],
            'telephone' => $data['telephone'],
            'genre'     => $data['genre'],
        ]);
        $etudiant->update([
            'ecole'  => $data['ecole'],
        ]);

        // Si un compte existe déjà, on peut prévenir que l'email n'est pas synchronisé
        if ($personnel->user && $personnel->user->email !== $personnel->email) {
            // Option : envoyer une notification
        }

        return redirect()->route('etudiants.index')->with('success', 'Étudiant mis à jour.');
    }
    // Synchronisation du compte utilisateur avec les infos du personnel
     public function syncAccount(Etudiant $etudiant) {
    $personnel = $etudiant->personnel;
        if (!$personnel->user) {
            return back()->with('error', 'Aucun compte utilisateur associé à ce personnel.');
     }
    $user = $personnel->user;
       if ($user->email !== $personnel->email) {
           $user->update(['email' => $personnel->email]);
           return back()->with('success', 'Email du compte utilisateur synchronisé avec le personnel.');
       }
         return back()->with('info', 'Le compte utilisateur est déjà à jour.');
     }

public function show(Etudiant $etudiant)
{
    $etudiant->load('personnel.user');
    return view('admin.etudiants.show', compact('etudiant'));
}
    public function destroy(Etudiant $etudiant) {
        $etudiant->personnel()->delete(); // Supprime le personnel (soft delete)
        $etudiant->delete();
        return redirect()->route('etudiants.index')->with('success', 'Étudiant supprimé.');
    }

    // Corbeille (soft delete)
    public function trash() {
        $etudiants = Etudiant::onlyTrashed()->with('personnel')->paginate(10);
        return view('admin.etudiants.trash', compact('etudiants'));
    }

    public function restore($id) {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->restore();
        $etudiant->personnel()->restore();
        return redirect()->route('etudiants.trash')->with('success', 'Étudiant restauré.');
    }

    public function forceDelete($id) {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->personnel()->forceDelete();
        $etudiant->forceDelete();
        return redirect()->route('etudiants.trash')->with('success', 'Étudiant définitivement supprimé.');
    }

    public function refresh()
{
    \Artisan::call('cache:clear');
    return redirect()->route('etudiants.index')->with('success', 'Cache vidé');
}
}