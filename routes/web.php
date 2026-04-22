<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StudentStageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminPresenceController;
use App\Http\Controllers\AdminAttendanceTrackingController;
use App\Http\Controllers\AdminReportTrackingController;
use App\Http\Controllers\SuperviseurDashboardController;
use App\Http\Controllers\DomaineController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('login'));

require __DIR__ . '/auth.php';

// Routes protégées
// Routes protégées (mdp change non requis)
Route::middleware(['auth', 'verified', \App\Http\Middleware\DecryptRouteParameter::class])->group(function () {

    // ---------------- Dashboard ----------------
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ---------------- Profil utilisateur ----------------
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // ---------------- Jours ----------------
    Route::prefix('admin/jours')->group(function () {
        Route::get('/', [JourController::class, 'index'])->name('jours.index')->middleware('permission:jour_stage.view');
        Route::get('create', [JourController::class, 'create'])->name('jours.create')->middleware('permission:jour_stage.create');
        Route::post('/', [JourController::class, 'store'])->name('jours.store')->middleware('permission:jour_stage.create');
        Route::get('{jour}/edit', [JourController::class, 'edit'])->name('jours.edit')->middleware('permission:jour_stage.edit');
        Route::put('{jour}', [JourController::class, 'update'])->name('jours.update')->middleware('permission:jour_stage.edit');
        Route::delete('{jour}', [JourController::class, 'destroy'])->name('jours.destroy')->middleware('permission:jour_stage.delete');
    });

    // ---------------- Étudiants ----------------
    Route::prefix('admin/etudiants')->group(function () {
        Route::get('/', [EtudiantController::class, 'index'])->name('etudiants.index')->middleware('permission:etudiants.view');
        Route::get('create', [EtudiantController::class, 'create'])->name('etudiants.create')->middleware('permission:etudiants.create');
        Route::post('/', [EtudiantController::class, 'store'])->name('etudiants.store')->middleware('permission:etudiants.create');
        Route::post('sync-accounts', [EtudiantController::class, 'syncAccounts'])->name('etudiants.syncAccounts')->middleware('permission:etudiants.edit');
        Route::get('{etudiant}/edit', [EtudiantController::class, 'edit'])->name('etudiants.edit')->middleware('permission:etudiants.edit');
        Route::put('{etudiant}', [EtudiantController::class, 'update'])->name('etudiants.update')->middleware('permission:etudiants.edit');
        Route::delete('{etudiant}', [EtudiantController::class, 'destroy'])->name('etudiants.destroy')->middleware('permission:etudiants.delete');
        Route::post('{etudiant}/sync-account', [EtudiantController::class, 'syncAccount'])->name('etudiants.syncAccount')->middleware('permission:etudiants.edit');

        // Corbeille
        Route::get('corbeille', [EtudiantController::class, 'trash'])->name('etudiants.trash')->middleware('permission:etudiants.view');
        Route::put('{id}/restore', [EtudiantController::class, 'restore'])->name('etudiants.restore')->middleware('permission:etudiants.restore');
        Route::delete('{id}/force-delete', [EtudiantController::class, 'forceDelete'])->name('etudiants.forceDelete')->middleware('permission:etudiants.force-delete');
    });

    // ---------------- Stages ----------------
    Route::prefix('admin/stages')->group(function () {
        Route::get('/', [StageController::class, 'index'])->name('stages.index')->middleware('permission:stages.view');
        Route::get('create', [StageController::class, 'create'])->name('stages.create')->middleware('permission:stages.create');
        Route::post('/', [StageController::class, 'store'])->name('stages.store')->middleware('permission:stages.create');

        // Corbeille
        Route::get('corbeille', [StageController::class, 'trash'])->name('stages.trash')->middleware('permission:stages.view');

        // Routes avec paramètres cryptés
        Route::get('{stage}', [StageController::class, 'show'])->name('stages.show')->middleware('permission:stages.view');
        Route::get('{stage}/edit', [StageController::class, 'edit'])->name('stages.edit')->middleware('permission:stages.edit');
        Route::put('{stage}', [StageController::class, 'update'])->name('stages.update')->middleware('permission:stages.edit');
        Route::delete('{stage}', [StageController::class, 'destroy'])->name('stages.destroy')->middleware('permission:stages.delete');

        // Badge
        Route::get('{stage}/badge', [BadgeController::class, 'show'])->name('admin.stages.badge.show')->middleware('permission:badges.view');
        Route::get('{stage}/badge/download', [BadgeController::class, 'download'])->name('stages.badge.download')->middleware('permission:badges.download');

        // Attestation
        Route::get('{stage}/attestation', [AttestationController::class, 'show'])->name('stages.attestation.show')->middleware('permission:attestation.view');
        Route::post('{stage}/attestation/store', [AttestationController::class, 'store'])->name('stages.attestation.store')->middleware('permission:attestation.create');
        Route::get('{stage}/attestation/download', [AttestationController::class, 'generatePDF'])->name('stages.attestation.download')->middleware('permission:attestation.download')->defaults('type', 'download');
        Route::get('{stage}/attestation/print', [AttestationController::class, 'generatePDF'])->name('stages.attestation.print')->middleware('permission:attestation.print')->defaults('type', 'print');

        // Corbeille actions
        Route::put('{id}/restore', [StageController::class, 'restore'])->name('stages.restore')->middleware('permission:stages.restore');
        Route::delete('{id}/force-delete', [StageController::class, 'forceDelete'])->name('stages.forceDelete')->middleware('permission:stages.force-delete');
    });

    // ---------------- TypeStages ----------------
    Route::prefix('admin/type_stages')->group(function () {
        Route::get('/', [TypeStageController::class, 'index'])->name('type_stages.index')->middleware('permission:type_stages.view');
        Route::get('create', [TypeStageController::class, 'create'])->name('type_stages.create')->middleware('permission:type_stages.create');
        Route::post('/', [TypeStageController::class, 'store'])->name('type_stages.store')->middleware('permission:type_stages.create');
        Route::get('{type_stage}/edit', [TypeStageController::class, 'edit'])->name('type_stages.edit')->middleware('permission:type_stages.edit');
        Route::put('{type_stage}', [TypeStageController::class, 'update'])->name('type_stages.update')->middleware('permission:type_stages.edit');
        Route::delete('{type_stage}', [TypeStageController::class, 'destroy'])->name('type_stages.destroy')->middleware('permission:type_stages.delete');
    });

    // ---------------- Badges ----------------
    Route::prefix('admin/badges')->group(function () {
        Route::get('/', [BadgeController::class, 'index'])->name('badges.index')->middleware('permission:badges.view');
        Route::get('create', [BadgeController::class, 'create'])->name('badges.create')->middleware('permission:badges.create');
        Route::post('/', [BadgeController::class, 'store'])->name('badges.store')->middleware('permission:badges.create');
        Route::get('{badge}/edit', [BadgeController::class, 'edit'])->name('badges.edit')->middleware('permission:badges.edit');
        Route::put('{badge}', [BadgeController::class, 'update'])->name('badges.update')->middleware('permission:badges.edit');
        Route::delete('{badge}', [BadgeController::class, 'destroy'])->name('badges.destroy')->middleware('permission:badges.delete');

        Route::put('{id}/restore', [CorbeilleController::class, 'restoreBadge'])->name('badges.restore')->middleware('permission:badges.restore');
        Route::delete('{id}/force-delete', [CorbeilleController::class, 'forceDeleteBadge'])->name('badges.force-delete')->middleware('permission:badges.force-delete');
    });

    // ---------------- Services ----------------
    Route::prefix('admin/services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index')->middleware('permission:services.view');
        Route::get('create', [ServiceController::class, 'create'])->name('services.create')->middleware('permission:services.create');
        Route::post('/', [ServiceController::class, 'store'])->name('services.store')->middleware('permission:services.create');
        Route::get('{service}/edit', [ServiceController::class, 'edit'])->name('services.edit')->middleware('permission:services.edit');
        Route::put('{service}', [ServiceController::class, 'update'])->name('services.update')->middleware('permission:services.edit');
        Route::delete('{service}', [ServiceController::class, 'destroy'])->name('services.destroy')->middleware('permission:services.delete');

        Route::patch('{id}/restore', [CorbeilleController::class, 'restoreService'])->name('services.restore')->middleware('permission:services.restore');
        Route::delete('{id}/force-delete', [CorbeilleController::class, 'forceDeleteService'])->name('services.force-delete')->middleware('permission:services.force-delete');
    });

    // ---------------- Domaines ----------------
    Route::prefix('admin/domaines')->group(function () {
        Route::get('/', [DomaineController::class, 'index'])->name('domaines.index')->middleware('permission:domaines.view');
        Route::get('create', [DomaineController::class, 'create'])->name('domaines.create')->middleware('permission:domaines.create');
        Route::post('/', [DomaineController::class, 'store'])->name('domaines.store')->middleware('permission:domaines.create');
        Route::get('{domaine}/edit', [DomaineController::class, 'edit'])->name('domaines.edit')->middleware('permission:domaines.edit');
        Route::get('{domaine}', [DomaineController::class, 'show'])->name('domaines.show')->middleware('permission:domaines.view');
        Route::put('{domaine}', [DomaineController::class, 'update'])->name('domaines.update')->middleware('permission:domaines.edit');
        Route::delete('{domaine}', [DomaineController::class, 'destroy'])->name('domaines.destroy')->middleware('permission:domaines.delete');
    });

    // ---------------- Employés par domaine ----------------
    Route::prefix('admin/employes')->group(function () {
        Route::get('domaine/{domaine}', [UserController::class, 'indexByDomaine'])->name('employes.by_domaine')->middleware('permission:users.view');
    });

    // ---------------- Sites ----------------
    Route::prefix('admin/sites')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('sites.index')->middleware('permission:sites.view');
        Route::get('create', [SiteController::class, 'create'])->name('sites.create')->middleware('permission:sites.create');
        Route::post('/', [SiteController::class, 'store'])->name('sites.store')->middleware('permission:sites.create');
        Route::get('{site}/edit', [SiteController::class, 'edit'])->name('sites.edit')->middleware('permission:sites.edit');
        Route::put('{site}', [SiteController::class, 'update'])->name('sites.update')->middleware('permission:sites.edit');
        Route::delete('{site}', [SiteController::class, 'destroy'])->name('sites.destroy')->middleware('permission:sites.delete');
    });

    // ---------------- Taches ----------------
    Route::prefix('admin/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index')->middleware('permission:tasks.view');
        Route::get('create', [TaskController::class, 'create'])->name('tasks.create')->middleware('permission:tasks.create');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store')->middleware('permission:tasks.create');
        Route::get('{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit')->middleware('permission:tasks.edit');
        Route::put('{task}', [TaskController::class, 'update'])->name('tasks.update')->middleware('permission:tasks.edit');
        Route::delete('{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')->middleware('permission:tasks.delete');
    });

    // ---------------- Signataires ----------------
    Route::prefix('admin/signataires')->group(function () {
        Route::get('/', [SignataireController::class, 'index'])->name('signataires.index')->middleware('permission:signataires.view');
        Route::get('create', [SignataireController::class, 'create'])->name('signataires.create')->middleware('permission:signataires.create');
        Route::post('/', [SignataireController::class, 'store'])->name('signataires.store')->middleware('permission:signataires.create');
        Route::get('{signataire}/edit', [SignataireController::class, 'edit'])->name('signataires.edit')->middleware('permission:signataires.edit');
        Route::put('{signataire}', [SignataireController::class, 'update'])->name('signataires.update')->middleware('permission:signataires.edit');
        Route::delete('{signataire}', [SignataireController::class, 'destroy'])->name('signataires.destroy')->middleware('permission:signataires.delete');
    });

    // ---------------- Users ----------------
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index')->middleware('permission:users.view');
        Route::get('create', [UserController::class, 'create'])->name('admin.users.create')->middleware('permission:users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store')->middleware('permission:users.create');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit')->middleware('permission:users.edit');
        Route::put('{user}', [UserController::class, 'update'])->name('admin.users.update')->middleware('permission:users.edit');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('admin.users.destroy')->middleware('permission:users.delete');

        Route::post('permissions', [UserController::class, 'createPermission'])->name('admin.permissions.store')->middleware('permission:users.create');

        // Corbeille - User restore and force delete
        Route::put('{id}/restore', [CorbeilleController::class, 'restoreUser'])->name('users.restore')->middleware('permission:users.restore');
        Route::delete('{id}/force-delete', [CorbeilleController::class, 'forceDeleteUser'])->name('users.forceDelete')->middleware('permission:users.force-delete');
    });

    // ---------------- Roles ----------------
    Route::prefix('admin/roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('admin.roles.index')->middleware('permission:roles.view');
        Route::get('create', [RoleController::class, 'create'])->name('admin.roles.create')->middleware('permission:roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('admin.roles.store')->middleware('permission:roles.create');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit')->middleware('permission:roles.edit');
        Route::put('{role}', [RoleController::class, 'update'])->name('admin.roles.update')->middleware('permission:roles.edit');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware('permission:roles.delete');
    });

    // ---------------- Corbeille Globale ----------------
    Route::get('/corbeille', [CorbeilleController::class, 'index'])->name('corbeille.index')->middleware('permission:corbeille.view');

    // ---------------- Espace stagiaire ----------------
    Route::get('/mon-stage', [StudentStageController::class, 'show'])
        ->name('student.stage');

    // ---------------- Dashboard Superviseur ----------------
    Route::prefix('superviseur')->middleware('role:superviseur')->group(function () {
        Route::get('/dashboard', [SuperviseurDashboardController::class, 'index'])
            ->name('superviseur.dashboard');
    });

    // ---------------- Presence ----------------
    Route::prefix('presence')->group(function () {
        Route::get('/pointage', [PresenceController::class, 'pointage'])->name('presence.pointage')->middleware('permission:presence.view');
        Route::get('/historique', [PresenceController::class, 'historique'])->name('presence.historique')->middleware('permission:presence.view');
        Route::post('/prepare-checkin', [PresenceController::class, 'prepareCheckIn'])->name('presence.prepareCheckin')->middleware('permission:presence.checkin');
        Route::post('/prepare-checkout', [PresenceController::class, 'prepareCheckOut'])->name('presence.prepareCheckout')->middleware('permission:presence.checkout');
        Route::get('/validate', [PresenceController::class, 'showValidation'])->name('presence.validate');
        Route::post('/confirm', [PresenceController::class, 'confirm'])->name('presence.confirm');
        Route::post('/check-in', [PresenceController::class, 'checkIn'])->name('presence.checkin')->middleware('permission:presence.checkin');
        Route::post('/check-out', [PresenceController::class, 'checkOut'])->name('presence.checkout')->middleware('permission:presence.checkout');
    });

    // ---------------- Rapports journaliers ----------------
    Route::prefix('reports')->group(function () {

        // accès lecture
        Route::get('/', [DailyReportController::class, 'index'])
            ->name('reports.index');

        // création (étudiant ou employé autorisé via logique controller)
        Route::post('/', [DailyReportController::class, 'store'])
            ->name('reports.store');
    });
    // ---------------- Supervision Présence Admin ----------------
    Route::prefix('admin/presence')->middleware('can:accessAdminPresence')->group(function () {
        Route::get('/', [AdminPresenceController::class, 'index'])->name('admin.presence.index');
        Route::get('/stats', [AdminPresenceController::class, 'stats'])->name('admin.presence.stats');
        Route::get('/dashboard-stats', [AdminPresenceController::class, 'dashboardStats'])->name('admin.presence.dashboard-stats');
        Route::get('/user-stats/{user}', [AdminPresenceController::class, 'userStats'])->name('admin.presence.user-stats');
        Route::get('/anomalies', [AdminPresenceController::class, 'anomalies'])->name('admin.presence.anomalies');
        Route::get('/pointage-suivi', [AdminPresenceController::class, 'pointageSuivi'])->name('admin.presence.pointage-suivi');
        Route::get('/export-pointages', [AdminPresenceController::class, 'exportPointages'])->name('admin.presence.export-pointages');
        Route::post('/{anomalyId}/resolve', [AdminPresenceController::class, 'resolveAnomaly'])
            ->name('admin.presence.anomalies.resolve')
            ->middleware('can:reviewAdminAnomalies');
        Route::get('/export', [AdminPresenceController::class, 'export'])->name('admin.presence.export');
    });

    // ---------------- Suivi des Pointages Admin ----------------
    Route::prefix('admin/attendance-tracking')->middleware('permission:presence.view')->group(function () {
        Route::get('/', [AdminAttendanceTrackingController::class, 'index'])->name('attendance.tracking.index');
        Route::get('/export', [AdminAttendanceTrackingController::class, 'export'])->name('attendance.tracking.export');
        Route::get('/user/{user}/historique', [AdminAttendanceTrackingController::class, 'userHistorique'])->name('attendance.tracking.user.historique');
    });

    // ---------------- Suivi des Rapports Admin ----------------
    Route::prefix('admin/reports')->middleware('permission:daily_reports.view')->group(function () {
        Route::get('/', [AdminReportTrackingController::class, 'index'])->name('admin.reports.index');
    });

    // ---------------- Notifications ----------------
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

        // 🔥 API JSON pour menu mobile dynamique
        Route::get('/unread-json', function () {
            $service = app(\App\Services\NotificationService::class);
            return response()->json([
                'count' => $service->getUnreadCount(),
                'notifications' => $service->getUnreadNotifications()->take(5)->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'title' => $notif->title,
                        'message' => $notif->message,
                        'color' => $notif->color ?? 'blue',
                        'created_at' => $notif->created_at->diffForHumans(),
                        'read_at' => $notif->read_at ? true : false,
                        'url' => $notif->url
                    ];
                })
            ]);
        })->name('notifications.unread.json');

        Route::post('/mark-read/{id}', function ($id) {
            $service = app(\App\Services\NotificationService::class);
            $service->markAsRead($id);
            return response()->json(['success' => true]);
        })->name('notifications.mark-read.api');

        Route::post('/mark-all-read-api', function () {
            $service = app(\App\Services\NotificationService::class);
            $service->markAllAsRead();
            return response()->json(['success' => true]);
        })->name('notifications.mark-all.api');
    });
});
