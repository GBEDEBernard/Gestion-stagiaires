<?php

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

use App\Models\User;

// Redirection vers login
Route::get('/', fn() => redirect()->route('login'));

// Register seulement si aucun utilisateur
if(User::count() === 0) {
    Route::get('/register', [RegisteredUserController::class,'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class,'store']);
}

Route::middleware(['auth', 'verified'])->group(function() {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Jours CRUD
    Route::resource('/admin/jours', JourController::class)
        ->names([
            'index'=>'jours.index',
            'create'=>'jours.create',
            'store'=>'jours.store',
            'edit'=>'jours.edit',
            'update'=>'jours.update',
            'destroy'=>'jours.destroy',
        ])
        ->middleware('permission:jour_stage.view|create|edit|delete');

    // Etudiants CRUD
    Route::resource('/admin/etudiants', EtudiantController::class)
        ->names([
            'index'=>'etudiants.index',
            'create'=>'etudiants.create',
            'store'=>'etudiants.store',
            'edit'=>'etudiants.edit',
            'update'=>'etudiants.update',
            'destroy'=>'etudiants.destroy',
        ])
        ->middleware('permission:etudiants.view|create|edit|delete');

    // Stages CRUD
    Route::resource('/admin/stages', StageController::class)
        ->names([
            'index'=>'stages.index',
            'create'=>'stages.create',
            'store'=>'stages.store',
            'edit'=>'stages.edit',
            'update'=>'stages.update',
            'destroy'=>'stages.destroy',
        ])
        ->middleware('permission:stages.view|create|edit|delete');

    // TypeStages CRUD
    Route::resource('/admin/type_stages', TypeStageController::class)
        ->names([
            'index'=>'type_stages.index',
            'create'=>'type_stages.create',
            'store'=>'type_stages.store',
            'edit'=>'type_stages.edit',
            'update'=>'type_stages.update',
            'destroy'=>'type_stages.destroy',
        ])
        ->middleware('permission:type_stages.view|create|edit|delete');

    // Badges CRUD
    Route::resource('/admin/badges', BadgeController::class)
        ->names([
            'index'=>'badges.index',
            'create'=>'badges.create',
            'store'=>'badges.store',
            'edit'=>'badges.edit',
            'update'=>'badges.update',
            'destroy'=>'badges.destroy',
        ])
        ->middleware('permission:badges.view|create|edit|delete');

    // Stagiaires show + badge
    Route::get('/admin/stage/{stagiaire}', [StageController::class, 'show'])
        ->name('stage.show');
        
  Route::get('/stages/{stage}/badge/download', [BadgeController::class, 'download'])->name('stages.badge.download');

         // Service CRUD
    Route::resource('/admin/services', ServiceController::class)
        ->names([
            'index'=>'services.index',
            'create'=>'services.create',
            'store'=>'services.store',
            'edit'=>'services.edit',
            'update'=>'services.update',
            'destroy'=>'services.destroy',
        ])
        ->middleware('permission:services.view|create|edit|delete');
// la route des service non fait par un etudiant
        Route::get('/etudiants/{etudiant}/services', [StageController::class, 'servicesDisponibles']);


# la route "simple-qrcode"
Route::get('/qr-site', [StageController::class, 'site'])->name('qr.site');

// Affichage du badge pour un stagiaire
Route::get('/admin/stages/{stage}/badge', [BadgeController::class, 'show'])->name('admin.stages.badge.show');


Route::get('/admin/stages/{stage}/attestation', [AttestationController::class, 'show'])
    ->name('stages.attestation.show');

Route::get('/stages/{stage}/attestation', [AttestationController::class, 'show'])->name('stages.attestation.show');
Route::post('/stages/{stage}/attestation/store', [AttestationController::class, 'store'])->name('stages.attestation.store');
Route::get('/stages/{stage}/attestation/download', [AttestationController::class, 'generatePDF'])
    ->name('stages.attestation.download')
    ->defaults('type', 'download');

Route::get('/stages/{stage}/attestation/print', [AttestationController::class, 'generatePDF'])
    ->name('stages.attestation.print')
    ->defaults('type', 'print');

    // Route our les signataires
    Route::resource('/admin/signataires', SignataireController::class)
    ->names([
        'index'   => 'signataires.index',
        'create'  => 'signataires.create',
        'store'   => 'signataires.store',
        'edit'    => 'signataires.edit',
        'update'  => 'signataires.update',
        'destroy' => 'signataires.destroy',
    ]);

});

require __DIR__.'/auth.php';
