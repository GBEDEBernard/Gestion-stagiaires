<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
<<<<<<< HEAD
use Illuminate\Http\Request;

class EtudiantController extends Controller
{
    public function index()
    {
        $etudiants = Etudiant::paginate(5);
=======
use App\Services\EtudiantAccountService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EtudiantController extends Controller
{
    public function __construct(
        protected EtudiantAccountService $etudiantAccountService
    ) {
    }

    public function index()
    {
        $etudiants = Etudiant::with('user')->paginate(5);

>>>>>>> e9635ab
        return view('admin.etudiants.index', compact('etudiants'));
    }

    public function create()
    {
<<<<<<< HEAD
        return view('admin.etudiants.create');
=======
        // jb -> La creation stagiaire passe desormais par le formulaire
        // utilisateur unifie, prefiltre sur le role etudiant.
        return redirect()->route('admin.users.create', ['role' => 'etudiant']);
>>>>>>> e9635ab
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
<<<<<<< HEAD
            'genre' => 'required',
=======
            'genre' => 'required|string|max:50',
>>>>>>> e9635ab
            'email' => 'required|email|unique:etudiants,email',
            'telephone' => 'nullable|string|max:20',
            'ecole' => 'nullable|string|max:255',
        ]);

<<<<<<< HEAD
        Etudiant::create($data);
        return redirect()->route('etudiants.index')->with('success', 'Étudiant créé avec succès.');
=======
        $etudiant = Etudiant::create($data);

        try {
            $account = $this->etudiantAccountService->ensureLinkedUser($etudiant);
        } catch (ValidationException $exception) {
            $etudiant->delete();
            throw $exception;
        }

        $message = "Etudiant cree avec succes. Compte de connexion rattache a {$account['user']->email}.";

        if ($account['temporary_password']) {
            $message .= " Mot de passe temporaire: {$account['temporary_password']}.";
        }

        if (!empty($account['verification_email_sent'])) {
            $message .= ' Un email de verification a ete envoye.';
        }

        return redirect()->route('etudiants.index')->with('success', $message);
>>>>>>> e9635ab
    }

    public function edit(Etudiant $etudiant)
    {
<<<<<<< HEAD
=======
        $etudiant->load('user');

        if ($etudiant->user) {
            // jb -> Des qu'un compte existe, la modification repasse par
            // l'ecran unifie pour garder un seul point d'entree admin.
            return redirect()->to(encrypted_route('admin.users.edit', $etudiant->user));
        }

>>>>>>> e9635ab
        return view('admin.etudiants.edit', compact('etudiant'));
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
<<<<<<< HEAD
            'genre' => 'required',
=======
            'genre' => 'required|string|max:50',
>>>>>>> e9635ab
            'email' => 'required|email|unique:etudiants,email,' . $etudiant->id,
            'telephone' => 'nullable|string|max:20',
            'ecole' => 'nullable|string|max:255',
        ]);

        $etudiant->update($data);
<<<<<<< HEAD
        return redirect()->route('etudiants.index')->with('success', 'Étudiant mis à jour.');
=======
        $account = $this->etudiantAccountService->ensureLinkedUser($etudiant);

        $message = "Etudiant mis a jour. Compte lie: {$account['user']->email}.";

        if ($account['temporary_password']) {
            $message .= " Mot de passe temporaire: {$account['temporary_password']}.";
        }

        if (!empty($account['verification_email_sent'])) {
            $message .= ' Le nouvel email doit etre verifie.';
        }

        return redirect()->route('etudiants.index')->with('success', $message);
>>>>>>> e9635ab
    }

    public function destroy(Etudiant $etudiant)
    {
        $etudiant->delete();
<<<<<<< HEAD
        return redirect()->route('etudiants.index')->with('success', 'Étudiant supprimé.');
    }

    // la méthode des corbeille
    // Méthode corbeille
    public function trash()
    {
        $etudiants = Etudiant::onlyTrashed()->paginate(5); // nom correct
        return view('admin.etudiants.corbeille', compact('etudiants'));
    }

    // restaurer la suppression
=======

        return redirect()->route('etudiants.index')->with('success', 'Etudiant supprime.');
    }

    public function syncAccounts()
    {
        $etudiants = Etudiant::with('user')->get();
        $results = $this->etudiantAccountService->syncMany($etudiants);
        $generatedAccounts = collect($results)
            ->filter(fn ($result) => !empty($result['temporary_password']))
            ->map(function ($result) {
                return [
                    'etudiant' => $result['etudiant_name'],
                    'email' => $result['user']->email,
                    'password' => $result['temporary_password'],
                    'verification_email_sent' => !empty($result['verification_email_sent']),
                ];
            })
            ->values()
            ->all();

        $message = count($generatedAccounts) > 0
            ? count($generatedAccounts) . ' compte(s) stagiaire cree(s) automatiquement.'
            : 'Tous les comptes stagiaires existants ont ete synchronises.';

        return redirect()
            ->route('etudiants.index')
            ->with('success', $message)
            ->with('generated_accounts', $generatedAccounts);
    }

    public function syncAccount(Etudiant $etudiant)
    {
        $result = $this->etudiantAccountService->ensureLinkedUser($etudiant);
        $generatedAccounts = [];
        $message = "Compte stagiaire synchronise pour {$etudiant->prenom} {$etudiant->nom}.";

        if (!empty($result['temporary_password'])) {
            $generatedAccounts[] = [
                'etudiant' => "{$etudiant->prenom} {$etudiant->nom}",
                'email' => $result['user']->email,
                'password' => $result['temporary_password'],
                'verification_email_sent' => !empty($result['verification_email_sent']),
            ];
            $message = "Compte stagiaire cree pour {$etudiant->prenom} {$etudiant->nom}.";
        }

        return redirect()
            ->route('etudiants.index')
            ->with('success', $message)
            ->with('generated_accounts', $generatedAccounts);
    }

    public function trash()
    {
        $etudiants = Etudiant::onlyTrashed()->paginate(5);

        return view('admin.etudiants.corbeille', compact('etudiants'));
    }

>>>>>>> e9635ab
    public function restore($id)
    {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->restore();

<<<<<<< HEAD
        return redirect()->route('etudiants.index')->with('success', 'Étudiant restauré avec succès 🚀');
    }

    // suppression définitive
=======
        return redirect()->route('etudiants.index')->with('success', 'Etudiant restaure avec succes.');
    }

>>>>>>> e9635ab
    public function forceDelete($id)
    {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->forceDelete();

<<<<<<< HEAD
        return redirect()->route('etudiants.trash')->with('success', 'Étudiant supprimé définitivement 🗑️');
=======
        return redirect()->route('etudiants.trash')->with('success', 'Etudiant supprime definitivement.');
>>>>>>> e9635ab
    }
}
