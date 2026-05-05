<?php

namespace App\Http\Controllers;
use App\Models\Stage;
use App\Models\Etudiant;

use Illuminate\Http\Request;

use App\Models\Badge;
use App\Models\User;
use App\Models\Service;

class CorbeilleController extends Controller
{
    public function index()
    {
        $stagesTrash = Stage::onlyTrashed()->get();
        $etudiantsTrash = Etudiant::onlyTrashed()->get();
        $badgesTrash = Badge::onlyTrashed()->get();
        $servicesTrash = Service::onlyTrashed()->get();
        $usersTrash = User::onlyTrashed()->get();

        return view('admin.corbeille.index', compact('stagesTrash', 'etudiantsTrash', 'badgesTrash', 'servicesTrash', 'usersTrash'));
    }

    // Stages
    public function restoreStage($id) { Stage::withTrashed()->findOrFail($id)->restore(); return back()->with('success','Stage restauré ✅'); }
    public function forceDeleteStage($id) { Stage::withTrashed()->findOrFail($id)->forceDelete(); return back()->with('success','Stage supprimé définitivement 🗑️'); }

    // Étudiants
    public function restoreEtudiant($id) { Etudiant::withTrashed()->findOrFail($id)->restore(); return back()->with('success','Étudiant restauré ✅'); }
    public function forceDeleteEtudiant($id) { Etudiant::withTrashed()->findOrFail($id)->forceDelete(); return back()->with('success','Étudiant supprimé définitivement 🗑️'); }

    // Badges
    public function restoreBadge($id) { Badge::withTrashed()->findOrFail($id)->restore(); return back()->with('success','Badge restauré ✅'); }
    public function forceDeleteBadge($id) { Badge::withTrashed()->findOrFail($id)->forceDelete(); return back()->with('success','Badge supprimé définitivement 🗑️'); }

    // Services
    public function restoreService($id) { Service::withTrashed()->findOrFail($id)->restore(); return back()->with('success','Service restauré ✅'); }
    public function forceDeleteService($id) { Service::withTrashed()->findOrFail($id)->forceDelete(); return back()->with('success','Service supprimé définitivement 🗑️'); }
     //Users
    public function restoreUser($id) { User::withTrashed()->findOrFail($id)->restore(); return back()->with('success','utilisateur restauré ✅'); }
    public function forceDeleteUser($id) { User::withTrashed()->findOrFail($id)->forceDelete(); return back()->with('success','Utilisateur supprimé définitivement 🗑️'); }
}
