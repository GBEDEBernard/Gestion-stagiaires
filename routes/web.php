<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JourController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\TypeStageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AttestationController;
use App\Http\Controllers\SignataireController;
use App\Http\Controllers\CorbeilleController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Routes publiques (login, register)
Route::get('/', fn() => redirect()->route('login'));

if (User::count() === 0) {
    Route::get('/register', [RegisteredUserController::class,'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class,'store']);
}

require __DIR__.'/auth.php'; // ✅ Doit rester en dehors du middleware auth

// 🔹 Routes protégées par auth + email verified
Route::middleware(['auth','verified'])->group(function() {

    // ---------------- Dashboard ----------------
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    // ---------------- Profil utilisateur ----------------
    Route::prefix('profile')->group(function() {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // ---------------- Jours ----------------
    Route::prefix('admin/jours')->group(function() {
        Route::get('/', [JourController::class,'index'])->name('jours.index')->middleware('permission:jour_stage.view');
        Route::get('create', [JourController::class,'create'])->name('jours.create')->middleware('permission:jour_stage.create');
        Route::post('/', [JourController::class,'store'])->name('jours.store')->middleware('permission:jour_stage.create');
        Route::get('{jour}/edit', [JourController::class,'edit'])->name('jours.edit')->middleware('permission:jour_stage.edit');
        Route::put('{jour}', [JourController::class,'update'])->name('jours.update')->middleware('permission:jour_stage.edit');
        Route::delete('{jour}', [JourController::class,'destroy'])->name('jours.destroy')->middleware('permission:jour_stage.delete');
    });

    // ---------------- Étudiants ----------------
    Route::prefix('admin/etudiants')->group(function() {
        Route::get('/', [EtudiantController::class,'index'])->name('etudiants.index')->middleware('permission:etudiants.view');
        Route::get('create', [EtudiantController::class,'create'])->name('etudiants.create')->middleware('permission:etudiants.create');
        Route::post('/', [EtudiantController::class,'store'])->name('etudiants.store')->middleware('permission:etudiants.create');
        Route::get('{etudiant}/edit', [EtudiantController::class,'edit'])->name('etudiants.edit')->middleware('permission:etudiants.edit');
        Route::put('{etudiant}', [EtudiantController::class,'update'])->name('etudiants.update')->middleware('permission:etudiants.edit');
        Route::delete('{etudiant}', [EtudiantController::class,'destroy'])->name('etudiants.destroy')->middleware('permission:etudiants.delete');

        // Corbeille
        Route::get('corbeille', [EtudiantController::class,'trash'])->name('etudiants.trash')->middleware('permission:etudiants.view');
        Route::put('{id}/restore', [EtudiantController::class,'restore'])->name('etudiants.restore')->middleware('permission:etudiants.restore');
        Route::delete('{id}/force-delete', [EtudiantController::class,'forceDelete'])->name('etudiants.forceDelete')->middleware('permission:etudiants.force-delete');
    });

    // ---------------- Stages ----------------
    Route::prefix('admin/stages')->group(function() {
        Route::get('/', [StageController::class,'index'])->name('stages.index')->middleware('permission:stages.view');
        Route::get('create', [StageController::class,'create'])->name('stages.create')->middleware('permission:stages.create');
        Route::post('/', [StageController::class,'store'])->name('stages.store')->middleware('permission:stages.create');
        Route::get('{stage}/edit', [StageController::class,'edit'])->name('stages.edit')->middleware('permission:stages.edit');
        Route::put('{stage}', [StageController::class,'update'])->name('stages.update')->middleware('permission:stages.edit');
        Route::delete('{stage}', [StageController::class,'destroy'])->name('stages.destroy')->middleware('permission:stages.delete');

        // Corbeille stages
        Route::get('corbeille', [StageController::class,'trash'])->name('stages.trash')->middleware('permission:stages.view');
        Route::put('{id}/restore', [StageController::class,'restore'])->name('stages.restore')->middleware('permission:stages.restore');
        Route::delete('{id}/force-delete', [StageController::class,'forceDelete'])->name('stages.forceDelete')->middleware('permission:stages.force-delete');

        // Badge
        Route::get('{stage}/badge', [BadgeController::class,'show'])->name('admin.stages.badge.show')->middleware('permission:badges.view');
        Route::get('{stage}/badge/download', [BadgeController::class,'download'])->name('stages.badge.download')->middleware('permission:badges.download');

        // Attestation
        Route::get('{stage}/attestation', [AttestationController::class,'show'])->name('stages.attestation.show')->middleware('permission:attestation.view');
        Route::post('{stage}/attestation/store', [AttestationController::class,'store'])->name('stages.attestation.store')->middleware('permission:attestation.create');
        Route::get('{stage}/attestation/download', [AttestationController::class,'generatePDF'])->name('stages.attestation.download')->middleware('permission:attestation.download')->defaults('type','download');
        Route::get('{stage}/attestation/print', [AttestationController::class,'generatePDF'])->name('stages.attestation.print')->middleware('permission:attestation.print')->defaults('type','print');
    });

    // ---------------- TypeStages ----------------
    Route::prefix('admin/type_stages')->group(function() {
        Route::get('/', [TypeStageController::class,'index'])->name('type_stages.index')->middleware('permission:type_stages.view');
        Route::get('create', [TypeStageController::class,'create'])->name('type_stages.create')->middleware('permission:type_stages.create');
        Route::post('/', [TypeStageController::class,'store'])->name('type_stages.store')->middleware('permission:type_stages.create');
        Route::get('{type_stage}/edit', [TypeStageController::class,'edit'])->name('type_stages.edit')->middleware('permission:type_stages.edit');
        Route::put('{type_stage}', [TypeStageController::class,'update'])->name('type_stages.update')->middleware('permission:type_stages.edit');
        Route::delete('{type_stage}', [TypeStageController::class,'destroy'])->name('type_stages.destroy')->middleware('permission:type_stages.delete');
    });

    // ---------------- Badges ----------------
    Route::prefix('admin/badges')->group(function() {
        Route::get('/', [BadgeController::class,'index'])->name('badges.index')->middleware('permission:badges.view');
        Route::get('create', [BadgeController::class,'create'])->name('badges.create')->middleware('permission:badges.create');
        Route::post('/', [BadgeController::class,'store'])->name('badges.store')->middleware('permission:badges.create');
        Route::get('{badge}/edit', [BadgeController::class,'edit'])->name('badges.edit')->middleware('permission:badges.edit');
        Route::put('{badge}', [BadgeController::class,'update'])->name('badges.update')->middleware('permission:badges.edit');
        Route::delete('{badge}', [BadgeController::class,'destroy'])->name('badges.destroy')->middleware('permission:badges.delete');
    Route::get('{stage}', [StageController::class,'show'])->name('stages.show')->middleware('permission:stages.view');

        // Corbeille badges
        Route::put('{id}/restore', [CorbeilleController::class,'restoreBadge'])->name('badges.restore')->middleware('permission:badges.restore');
        Route::delete('{id}/force-delete', [CorbeilleController::class,'forceDeleteBadge'])->name('badges.force-delete')->middleware('permission:badges.force-delete');
    });

    // ---------------- Services ----------------
    Route::prefix('admin/services')->group(function() {
        Route::get('/', [ServiceController::class,'index'])->name('services.index')->middleware('permission:services.view');
        Route::get('create', [ServiceController::class,'create'])->name('services.create')->middleware('permission:services.create');
        Route::post('/', [ServiceController::class,'store'])->name('services.store')->middleware('permission:services.create');
        Route::get('{service}/edit', [ServiceController::class,'edit'])->name('services.edit')->middleware('permission:services.edit');
        Route::put('{service}', [ServiceController::class,'update'])->name('services.update')->middleware('permission:services.edit');
        Route::delete('{service}', [ServiceController::class,'destroy'])->name('services.destroy')->middleware('permission:services.delete');

        // Corbeille services
        Route::patch('{id}/restore', [CorbeilleController::class,'restoreService'])->name('services.restore')->middleware('permission:services.restore');
        Route::delete('{id}/force-delete', [CorbeilleController::class,'forceDeleteService'])->name('services.force-delete')->middleware('permission:services.force-delete');
    });

    // ---------------- Signataires ----------------
    Route::prefix('admin/signataires')->group(function() {
        Route::get('/', [SignataireController::class,'index'])->name('signataires.index')->middleware('permission:signataires.view');
        Route::get('create', [SignataireController::class,'create'])->name('signataires.create')->middleware('permission:signataires.create');
        Route::post('/', [SignataireController::class,'store'])->name('signataires.store')->middleware('permission:signataires.create');
        Route::get('{signataire}/edit', [SignataireController::class,'edit'])->name('signataires.edit')->middleware('permission:signataires.edit');
        Route::put('{signataire}', [SignataireController::class,'update'])->name('signataires.update')->middleware('permission:signataires.edit');
        Route::delete('{signataire}', [SignataireController::class,'destroy'])->name('signataires.destroy')->middleware('permission:signataires.delete');
    });

    // ---------------- Users ----------------
    Route::prefix('admin/users')->group(function() {
        Route::get('/', [UserController::class,'index'])->name('admin.users.index')->middleware('permission:users.view');
        Route::get('create', [UserController::class,'create'])->name('admin.users.create')->middleware('permission:users.create');
        Route::post('/', [UserController::class,'store'])->name('admin.users.store')->middleware('permission:users.create');
        Route::get('{user}/edit', [UserController::class,'edit'])->name('admin.users.edit')->middleware('permission:users.edit');
        Route::put('{user}', [UserController::class,'update'])->name('admin.users.update')->middleware('permission:users.edit');
        Route::delete('{user}', [UserController::class,'destroy'])->name('admin.users.destroy')->middleware('permission:users.delete');

        // Créer une permission
        Route::post('permissions', [UserController::class,'createPermission'])->name('admin.permissions.store')->middleware('permission:users.create');
    });

    // ---------------- Corbeille Globale ----------------
    Route::get('/corbeille', [CorbeilleController::class,'index'])->name('corbeille.index')->middleware('permission:corbeille.view');

});
