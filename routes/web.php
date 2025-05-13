<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JourController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\TypeStageController;

Route::get('bloglayouts',[AccueilController::class , 'layouts'])->name('bloglayouts');
Route::get('/welcome',[AccueilController::class, 'acceuil'])->name('welcome');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
///la route pour entité ecole

Route::middleware(['auth'])->group(function () {
    // Liste des écoles
    Route::get('/admin/jours/index', [JourController::class, 'index'])->name('jours.index');

    // Formulaire de création
    Route::get('/admin/jours/create', [JourController::class, 'create'])->name('jours.create');

    // Stocker une nouvelle école
    Route::post('/admin/jours', [JourController::class, 'store'])->name('jours.store');

    // Formulaire de modification
    Route::get('/admin/jours/{jour}/edit', [JourController::class, 'edit'])->name('jours.edit');

    // Mettre à jour l’école
    Route::put('/admin/jours/{jour}', [JourController::class, 'update'])->name('jours.update');

    // Supprimer l’école
    Route::delete('/admin/jours/{jour}', [JourController::class, 'destroy'])->name('jours.destroy');
});

Route::middleware(['auth'])->group(function(){
    //route pour creer stagiaire
    Route::get('/admin/stagiaires/create', [StagiaireController::class, 'create'])->name('stagiaires.create');

    //la route pour afficher les stagiaires  (liste des stagiaires)
    Route::get('/admin/stagiaires/index', [StagiaireController::class , 'index'])->name('stagiaires.index');

       // Stocker un nouveaux stagiaires
     Route::post('/admin/stagiaires', [StagiaireController::class, 'store'])->name('stagiaires.store');

     // Formulaire de modification
    Route::get('/admin/stagiaires/{stagiaire}/edit', [StagiaireController::class, 'edit'])->name('stagiaires.edit');

    // Mettre à jour l’école
    Route::put('/admin/stagiaires/{stagiaire}', [StagiaireController::class, 'update'])->name('stagiaires.update');

    // Supprimer l’école
    Route::delete('/admin/stagiaires/{stagiaire}', [StagiaireController::class, 'destroy'])->name('stagiaires.destroy');

});

Route::middleware(['auth'])->group(function(){
    //route pour creer stagiaire
    Route::get('/admin/type_stages/create', [TypeStageController::class, 'create'])->name('type_stages.create');

    //la route pour afficher les stagiaires  (liste des stagiaires)
    Route::get('/admin/type_stages/index', [TypeStageController::class , 'index'])->name('type_stages.index');
    
       // Stocker un nouveaux stagiaires
     Route::post('/admin/type_stages', [TypeStageController::class, 'store'])->name('type_stages.store');

     // Formulaire de modification
    Route::get('/admin/type_stages/{type_stages}/edit', [TypeStageController::class, 'edit'])->name('type_stages.edit');

    // Mettre à jour l’école
    Route::put('/admin/type_stages/{type_stages}', [TypeStageController::class, 'update'])->name('type_stages.update');

    // Supprimer un type de stage
Route::delete('/admin/type_stages/{type_stages}', [TypeStageController::class, 'destroy'])->name('type_stages.destroy');

});

Route::middleware(['auth'])->group(function(){
    //route pour creer stagiaire
    Route::get('/admin/badges/create', [BadgeController::class, 'create'])->name('badges.create');

    //la route pour afficher les stagiaires  (liste des stagiaires)
    Route::get('/admin/badges/index', [BadgeController::class , 'index'])->name('badges.index');
    
       // Stocker un nouveaux stagiaires
     Route::post('/admin/badges', [BadgeController::class, 'store'])->name('badges.store');

     // Formulaire de modification
    Route::get('/admin/badges/{badges}/edit', [BadgeController::class, 'edit'])->name('badges.edit');

    // Mettre à jour l’école
    Route::put('/admin/badges/{badges}', [BadgeController::class, 'update'])->name('badges.update');

    // Supprimer l’école
    Route::delete('/admin/badges/{badges}', [BadgeController::class, 'destroy'])->name('badges.destroy');

});