<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
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

        return view('admin.etudiants.index', compact('etudiants'));
    }

    public function create()
    {
        // jb -> La creation stagiaire passe desormais par le formulaire
        // utilisateur unifie, prefiltre sur le role etudiant.
        return redirect()->route('admin.users.create', ['role' => 'etudiant']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'genre' => 'required|string|max:50',
            'email' => 'required|email|unique:etudiants,email',
            'telephone' => 'nullable|string|max:20',
            'ecole' => 'nullable|string|max:255',
        ]);

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
    }

    public function edit(Etudiant $etudiant)
    {
        $etudiant->load('user');

        if ($etudiant->user) {
            // jb -> Des qu'un compte existe, la modification repasse par
            // l'ecran unifie pour garder un seul point d'entree admin.
            return redirect()->to(encrypted_route('admin.users.edit', $etudiant->user));
        }

        return view('admin.etudiants.edit', compact('etudiant'));
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'genre' => 'required|string|max:50',
            'email' => 'required|email|unique:etudiants,email,' . $etudiant->id,
            'telephone' => 'nullable|string|max:20',
            'ecole' => 'nullable|string|max:255',
        ]);

        $etudiant->update($data);
        $account = $this->etudiantAccountService->ensureLinkedUser($etudiant);

        $message = "Etudiant mis a jour. Compte lie: {$account['user']->email}.";

        if ($account['temporary_password']) {
            $message .= " Mot de passe temporaire: {$account['temporary_password']}.";
        }

        if (!empty($account['verification_email_sent'])) {
            $message .= ' Le nouvel email doit etre verifie.';
        }

        return redirect()->route('etudiants.index')->with('success', $message);
    }

    public function destroy(Etudiant $etudiant)
    {
        $etudiant->delete();

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

    public function restore($id)
    {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->restore();

        return redirect()->route('etudiants.index')->with('success', 'Etudiant restaure avec succes.');
    }

    public function forceDelete($id)
    {
        $etudiant = Etudiant::onlyTrashed()->findOrFail($id);
        $etudiant->forceDelete();

        return redirect()->route('etudiants.trash')->with('success', 'Etudiant supprime definitivement.');
    }
}
