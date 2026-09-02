<?php

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\AttendanceException;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\EvaluationCriterion;
use App\Models\Personnel;
use App\Models\Site;
use App\Models\Stage;
use App\Models\TypeStage;
use App\Models\User;
use App\Services\StageEvaluationService;
use App\Services\StageReportService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->evaluations = app(StageEvaluationService::class);
    $this->reports     = app(StageReportService::class);
});

function pdfUser(string $role = 'admin', ?string $createdAt = null): User
{
    $personnel = Personnel::create([
        'nom'    => 'Pdf',
        'prenom' => ucfirst($role),
        'email'  => "{$role}." . Str::random(6) . '@example.com',
    ]);

    $user = User::create([
        'personnel_id'      => $personnel->id,
        'name'              => $personnel->full_name,
        'email'             => $personnel->email,
        'email_verified_at' => now(),
        'password'          => Hash::make('password'),
        'status'            => 'actif',
    ]);

    $user->assignRole($role);

    if ($createdAt) {
        $user->forceFill(['created_at' => $createdAt])->save();
        $personnel->forceFill(['date_debut_pointage' => $createdAt])->save();
    }

    return $user;
}

function pdfStage(User $studentUser): Stage
{
    $etudiant = Etudiant::create(['personnel_id' => $studentUser->personnel_id, 'ecole' => 'Test']);

    Personnel::where('id', $studentUser->personnel_id)->update([
        'personnable_type' => Etudiant::class,
        'personnable_id'   => $etudiant->id,
    ]);

    return Stage::create([
        'etudiant_id'  => $etudiant->id,
        'typestage_id' => TypeStage::create(['code' => Str::upper(Str::random(4)), 'libelle' => 'Academique'])->id,
        'domaine_id'   => Domaine::create(['nom' => 'Info ' . Str::random(4)])->id,
        'site_id'      => Site::create(['code' => 'S' . Str::random(4), 'name' => 'Siege', 'is_active' => true])->id,
        'theme'        => 'Sujet du stage',
        'date_debut'   => now()->subMonths(2)->startOfMonth()->toDateString(),
        'date_fin'     => now()->subMonth()->endOfMonth()->toDateString(),
    ]);
}

/** Note le stage et fige l'évaluation. */
function finalise($service, Stage $stage, User $admin, float $note = 14): void
{
    EvaluationCriterion::firstOrCreate(['label' => 'Critere'], ['weight' => 1]);

    $evaluation = $service->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();

    $service->saveScores($evaluation, [$row->id => ['score' => $note, 'comment' => null]], 'Bon stage.', null);
    $service->finalize($evaluation->fresh('scores'), $admin);
}

test('the admin can download the report as a pdf', function () {
    $admin   = pdfUser('admin');
    $student = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = pdfStage($student);

    $response = $this->actingAs($admin)->get(route('stages.rapport.pdf', $stage));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect($response->headers->get('content-disposition'))->toContain('attachment');
    // Un PDF commence toujours par cette signature
    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');
});

test('the print route serves the same document inline', function () {
    $admin   = pdfUser('admin');
    $student = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = pdfStage($student);

    $response = $this->actingAs($admin)->get(route('stages.rapport.print', $stage));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('inline');
});

test('the pdf reads the frozen snapshot instead of recomputing', function () {
    $admin   = pdfUser('admin');
    $student = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = pdfStage($student);

    $date = \Carbon\Carbon::parse($stage->date_debut);
    for ($i = 0; $i < 12; $i++) {
        if (!$date->isWeekend()) {
            AttendanceDay::create([
                'stage_id' => $stage->id, 'etudiant_id' => $stage->etudiant_id, 'user_id' => $student->id,
                'attendance_date' => $date->toDateString(),
                'first_check_in_at' => $date->copy()->setTime(8, 0),
                'last_check_out_at' => $date->copy()->setTime(17, 0),
                'arrival_status' => 'on_time',
            ]);
        }
        $date->addDay();
    }

    finalise($this->evaluations, $stage, $admin);

    $before = $this->reports->forDocument($stage->fresh())['counts']['expected_days'];

    // Une correction d'absence saisie après la remise du document
    AttendanceException::create([
        'user_id'         => $student->id,
        'attendance_date' => \Carbon\Carbon::parse($stage->date_debut)->addDays(1)->toDateString(),
        'reason'          => 'Correction tardive',
    ]);

    $after = $this->reports->forDocument($stage->fresh())['counts']['expected_days'];

    // Le document réimprimé doit rester identique à celui déjà remis
    expect($after)->toBe($before);

    // Alors qu'un calcul en direct, lui, a bien bougé
    expect($this->reports->build($stage->fresh())['counts']['expected_days'])->not->toBe($before);
});

test('an unfinalised report is computed live and marked provisional', function () {
    $admin   = pdfUser('admin');
    $student = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = pdfStage($student);

    $data = $this->reports->forDocument($stage);

    expect($data['is_frozen'])->toBeFalse()
        ->and($data['frozen_at'])->toBeNull();
});

test('the pdf uses the disclosure level chosen at finalisation, not the url', function () {
    $admin   = pdfUser('admin');
    $student = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = pdfStage($student);

    AttendanceAnomaly::create([
        'stage_id' => $stage->id, 'etudiant_id' => $stage->etudiant_id, 'user_id' => $student->id,
        'anomaly_type' => 'shared_device_detected', 'severity' => 'medium', 'status' => 'open',
        'detected_at' => \Carbon\Carbon::parse($stage->date_debut)->addDays(2),
    ]);

    EvaluationCriterion::firstOrCreate(['label' => 'Critere'], ['weight' => 1]);
    $evaluation = $this->evaluations->draftFor($stage, $admin);
    $row        = $evaluation->scores->first();

    // L'admin choisit explicitement de ne montrer que le décompte
    $this->evaluations->saveScores($evaluation, [$row->id => ['score' => 14, 'comment' => null]], null, 'count');
    $this->evaluations->finalize($evaluation->fresh('scores'), $admin);

    // Même en forçant le paramètre, le document remis garde le niveau retenu
    $response = $this->actingAs($admin)->get(route('stages.rapport.pdf', $stage) . '?anomalies=detailed');

    $response->assertOk();
    expect($stage->fresh()->evaluation->anomaly_disclosure)->toBe('count');
});

test('a student cannot download the report themselves', function () {
    // Le PDF est produit par l'administration puis remis en main propre.
    $admin   = pdfUser('admin');
    $student = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $stage   = pdfStage($student);

    finalise($this->evaluations, $stage, $admin);

    $response = $this->actingAs($student->fresh())->get(route('stages.rapport.pdf', $stage));
    expect($response->status())->not->toBe(200);
});

test('a student cannot download another student report', function () {
    $admin  = pdfUser('admin');
    $mine   = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $other  = pdfUser('etudiant', now()->subMonths(3)->toDateString());
    $stage  = pdfStage($other);

    finalise($this->evaluations, $stage, $admin);

    $response = $this->actingAs($mine)->get(route('stages.rapport.pdf', $stage));
    expect($response->status())->not->toBe(200);
});
