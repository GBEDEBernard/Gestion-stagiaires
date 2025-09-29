<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ------------------ INDEX ------------------
    public function index()
    {
        $users = User::with(['roles', 'permissions'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // ------------------ CREATE ------------------
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    // ------------------ STORE ------------------
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);

        // 🔹 Vider le cache des permissions pour que tout prenne effet immédiatement
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->forgetCachedPermissions();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur créé avec succès.');
    }

    // ------------------ EDIT ------------------
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    // ------------------ UPDATE ------------------
    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'permissions' => 'array',
        ]);

        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);

        // 🔹 Vider le cache pour que les changements prennent effet immédiatement
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->forgetCachedPermissions();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Rôles et permissions mis à jour avec succès');
    }

    // ------------------ DESTROY ------------------
    public function destroy(User $user)
    {
        $user->delete();

        // 🔹 Vider le cache après suppression
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé');
    }

    // ------------------ CREATE NEW PERMISSION ------------------
    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name|max:100',
        ]);

        Permission::create(['name' => $request->name]);

        // 🔹 Vider le cache après création de permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', 'Permission créée avec succès.');
    }
}
