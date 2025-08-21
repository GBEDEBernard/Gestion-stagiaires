<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JourController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\TypeStageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;

Route::get('bloglayouts',[AccueilController::class , 'layouts'])->name('bloglayouts');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
///la route pour entité ecole

Route::middleware(['auth'])->group(function () {

    // === JOURS ===
    Route::get('/admin/jours/index', [JourController::class, 'index'])
        ->name('jours.index')->middleware('permission:jours.view');

    Route::get('/admin/jours/create', [JourController::class, 'create'])
        ->name('jours.create')->middleware('permission:jours.create');

    Route::post('/admin/jours', [JourController::class, 'store'])
        ->name('jours.store')->middleware('permission:jours.create');

    Route::get('/admin/jours/{jour}/edit', [JourController::class, 'edit'])
        ->name('jours.edit')->middleware('permission:jours.edit');

    Route::put('/admin/jours/{jour}', [JourController::class, 'update'])
        ->name('jours.update')->middleware('permission:jours.edit');

    Route::delete('/admin/jours/{jour}', [JourController::class, 'destroy'])
        ->name('jours.destroy')->middleware('permission:jours.delete');

    // === STAGIAIRES ===
    Route::get('/admin/stagiaires/index', [StagiaireController::class , 'index'])
        ->name('stagiaires.index')->middleware('permission:stagiaires.view');

    Route::get('/admin/stagiaires/create', [StagiaireController::class, 'create'])
        ->name('stagiaires.create')->middleware('permission:stagiaires.create');

    Route::post('/admin/stagiaires', [StagiaireController::class, 'store'])
        ->name('stagiaires.store')->middleware('permission:stagiaires.create');

    Route::get('/admin/stagiaires/{stagiaire}/edit', [StagiaireController::class, 'edit'])
        ->name('stagiaires.edit')->middleware('permission:stagiaires.edit');

    Route::put('/admin/stagiaires/{stagiaire}', [StagiaireController::class, 'update'])
        ->name('stagiaires.update')->middleware('permission:stagiaires.edit');

    Route::delete('/admin/stagiaires/{stagiaire}', [StagiaireController::class, 'destroy'])
        ->name('stagiaires.destroy')->middleware('permission:stagiaires.delete');


        // la vue show profil route
        Route::get('/admin/stagiaires/{stagiaire}', [StagiaireController::class, 'show'])
             ->name('stagiaires.show')
          ->middleware('permission:stagiaires.view');
    //   route pour afficher les badge
    
Route::get('/admin/stagiaires/{stagiaire}/badge', [StagiaireController::class, 'badge'])
    ->name('stagiaires.badge');

    // === TYPE STAGES ===
    Route::get('/admin/type_stages/index', [TypeStageController::class , 'index'])
        ->name('type_stages.index')->middleware('permission:type_stages.view');

    Route::get('/admin/type_stages/create', [TypeStageController::class, 'create'])
        ->name('type_stages.create')->middleware('permission:type_stages.create');

    Route::post('/admin/type_stages', [TypeStageController::class, 'store'])
        ->name('type_stages.store')->middleware('permission:type_stages.create');

    Route::get('/admin/type_stages/{type_stages}/edit', [TypeStageController::class, 'edit'])
        ->name('type_stages.edit')->middleware('permission:type_stages.edit');

    Route::put('/admin/type_stages/{type_stages}', [TypeStageController::class, 'update'])
        ->name('type_stages.update')->middleware('permission:type_stages.edit');

    Route::delete('/admin/type_stages/{type_stages}', [TypeStageController::class, 'destroy'])
        ->name('type_stages.destroy')->middleware('permission:type_stages.delete');

    // === BADGES ===
    Route::get('/admin/badges/index', [BadgeController::class , 'index'])
        ->name('badges.index')->middleware('permission:badges.view');

    Route::get('/admin/badges/create', [BadgeController::class, 'create'])
        ->name('badges.create')->middleware('permission:badges.create');

    Route::post('/admin/badges', [BadgeController::class, 'store'])
        ->name('badges.store')->middleware('permission:badges.create');

    Route::get('/admin/badges/{badges}/edit', [BadgeController::class, 'edit'])
        ->name('badges.edit')->middleware('permission:badges.edit');

    Route::put('/admin/badges/{badges}', [BadgeController::class, 'update'])
        ->name('badges.update')->middleware('permission:badges.edit');

    Route::delete('/admin/badges/{badges}', [BadgeController::class, 'destroy'])
        ->name('badges.destroy')->middleware('permission:badges.delete');

    // === CONTACTS ===
    Route::get('/admin/contacts/index', [ContactController::class, 'afficher'])
        ->name('contacts.index')->middleware('permission:contacts.view');

    Route::get('/contact', function () {
        return view('contact');
    })->name('contact');

    // === INFOS PUBLIQUES ===
    Route::get('apropos', [AccueilController::class, 'apropo'])->name('apropos');
    Route::get('fonctionalite', [AccueilController::class, 'fonction'])->name('fonctionalite');
});