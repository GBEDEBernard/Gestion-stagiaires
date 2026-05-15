<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Domaine;
use App\Models\Employe;

class UserController extends Controller
{
    public function __construct(protected RolePermissionPresetService $roleService) {}

    public function index() {
        $users = User::with('personnel')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user) {
        $user->load('personnel');
        $roles = $this->roleService->orderedRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['actif', 'inactif'])],
            'password' => 'nullable|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
                'must_change_password' => true,
                'temporary_password_created_at' => now(),
                'password_changed_at' => null,
            ]);
            // Envoyer notification si souhaité
        }

        $user->update(['status' => $validated['status']]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Compte utilisateur mis à jour.');
    }

    public function destroy(User $user) {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }

    public function show(User $user) {
        // Redirige vers le profil approprié (étudiant ou employé)
        $profil = $user->profil();
        if ($profil instanceof \App\Models\Etudiant) {
            return redirect()->route('etudiants.index', ['etudiant' => $profil->id]);
        } elseif ($profil instanceof \App\Models\Employe) {
            return redirect()->route('employes.index', ['employe' => $profil->id]);
        }
        return redirect()->route('admin.users.index');
    }
    // UserController.php
public function indexByDomaine(Domaine $domaine)
{
    $employes = Employe::where('domaine_id', $domaine->id)
        ->with('personnel.user')
        ->paginate(10);
    return view('admin.employes.by_domaine', compact('domaine', 'employes'));
}
}