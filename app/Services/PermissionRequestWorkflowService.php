<?php

namespace App\Services;

use App\Mail\PermissionRequestReviewMail;
use App\Mail\PermissionRequestSignatoryMail;
use App\Models\AppNotification;
use App\Models\PermissionRequest;
use App\Models\Signataire;
use App\Models\Stage;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PermissionRequestWorkflowService
{
    public function resolveContext(User $user): array
    {
        $user->loadMissing('etudiant', 'domaine.creator');

        $stage = null;
        $domaine = $user->domaine;
        $approver = null;

        if ($user->hasRole('etudiant') && $user->etudiant) {
            $stage = Stage::query()
                ->with(['supervisor', 'domaine.creator', 'site'])
                ->active()
                ->where('etudiant_id', $user->etudiant->id)
                ->latest('date_debut')
                ->first();

            $domaine = $stage?->domaine;
            $approver = $stage?->supervisor ?: $domaine?->creator;
        } elseif ($user->hasRole('employe')) {
            $approver = $domaine?->creator;
        }

        if ($approver && $approver->is($user)) {
            $approver = null;
        }

        return [
            'stage' => $stage,
            'domaine' => $domaine,
            'approver' => $approver,
            'active_signataires' => $this->activeSignataires(),
        ];
    }

    public function submit(PermissionRequest $permissionRequest, bool $asDraft = false): PermissionRequest
    {
        if ($asDraft) {
            $permissionRequest->forceFill([
                'status' => PermissionRequest::STATUS_DRAFT,
                'submitted_at' => null,
            ])->save();

            return $permissionRequest->fresh();
        }

        $permissionRequest->forceFill([
            'submitted_at' => now(),
            'status' => PermissionRequest::STATUS_UNDER_REVIEW,
            'first_approval_status' => 'pending',
        ])->save();

        $this->notifyReviewStakeholders($permissionRequest);

        return $permissionRequest->fresh();
    }

    public function approve(PermissionRequest $permissionRequest, User $reviewer, ?string $notes = null): PermissionRequest
    {
        $permissionRequest->forceFill([
            'status' => PermissionRequest::STATUS_APPROVED,
            'first_approval_status' => 'approved',
            'reviewed_by_id' => $reviewer->id,
            'first_review_notes' => $notes,
            'first_reviewed_at' => now(),
            'approved_at' => now(),
        ])->save();

        $this->dispatchToSignataires($permissionRequest);

        return $permissionRequest->fresh();
    }

    public function reject(PermissionRequest $permissionRequest, User $reviewer, ?string $notes = null): PermissionRequest
    {
        $permissionRequest->forceFill([
            'status' => PermissionRequest::STATUS_REJECTED,
            'first_approval_status' => 'rejected',
            'reviewed_by_id' => $reviewer->id,
            'first_review_notes' => $notes,
            'first_reviewed_at' => now(),
            'rejected_at' => now(),
        ])->save();

        return $permissionRequest->fresh();
    }

    public function ensurePdf(PermissionRequest $permissionRequest): string
    {
        $permissionRequest->loadMissing(['requester.domaine', 'stage.site', 'stage.supervisor', 'domaine', 'firstApprover', 'reviewer']);

        if ($permissionRequest->pdf_path && Storage::disk('local')->exists($permissionRequest->pdf_path)) {
            return $permissionRequest->pdf_path;
        }

        $fileName = sprintf(
            'permission-requests/permission-request-%d-%s.pdf',
            $permissionRequest->id,
            now()->format('YmdHis')
        );

        $pdf = Pdf::loadView('permission-requests.pdf', [
            'permissionRequest' => $permissionRequest,
        ])->setPaper('a4');

        Storage::disk('local')->put($fileName, $pdf->output());

        $permissionRequest->forceFill([
            'pdf_path' => $fileName,
            'pdf_generated_at' => now(),
        ])->save();

        return $fileName;
    }

    public function dispatchToSignataires(PermissionRequest $permissionRequest): int
    {
        $signataires = $this->activeSignataires();

        if ($signataires->isEmpty()) {
            return 0;
        }

        $pdfPath = $this->ensurePdf($permissionRequest);

        foreach ($signataires as $signataire) {
            Mail::to($signataire->email)->send(new PermissionRequestSignatoryMail($permissionRequest->fresh(), $signataire, $pdfPath));
        }

        $permissionRequest->forceFill([
            'status' => PermissionRequest::STATUS_SENT,
            'sent_at' => now(),
            'signataires_snapshot' => $signataires
                ->map(fn(Signataire $signataire) => [
                    'id' => $signataire->id,
                    'nom' => $signataire->nom,
                    'poste' => $signataire->poste,
                    'email' => $signataire->email,
                    'ordre' => $signataire->ordre,
                ])
                ->values()
                ->all(),
        ])->save();

        return $signataires->count();
    }

    public function notifyReviewStakeholders(PermissionRequest $permissionRequest): int
    {
        $permissionRequest->loadMissing([
            'requester',
            'stage.site',
            'stage.supervisor',
            'domaine',
            'firstApprover',
        ]);

        $pdfPath = $this->ensurePdf($permissionRequest);
        $reviewUrl = route('permission-requests.show', $permissionRequest);

        $reviewers = collect();

        if ($permissionRequest->firstApprover) {
            $reviewers->push($permissionRequest->firstApprover);
        }

        $admins = User::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->get();

        $reviewers = $reviewers
            ->merge($admins)
            ->filter(fn(User $recipient) => $recipient->id !== $permissionRequest->user_id)
            ->unique('id')
            ->values();

        foreach ($reviewers as $recipient) {
            $roleLabel = $recipient->hasRole('admin')
                ? 'administrateur'
                : ($recipient->hasRole('superviseur') ? 'superviseur' : 'validateur');

            if (!empty($recipient->email)) {
                Mail::to($recipient->email)->send(
                    new PermissionRequestReviewMail(
                        $permissionRequest->fresh(),
                        $recipient,
                        $pdfPath,
                        $roleLabel
                    )
                );
            }

            AppNotification::updateOrCreate(
                ['unique_id' => 'permission_request_submitted_' . $permissionRequest->id . '_user_' . $recipient->id],
                [
                    'user_id' => $recipient->id,
                    'type' => 'permission_request_submitted',
                    'title' => 'Nouvelle demande de permission',
                    'message' => $permissionRequest->requester->name . ' a soumis une demande de permission.',
                    'icon' => 'file-signature',
                    'color' => 'blue',
                    'url' => $reviewUrl,
                    'reference_id' => $permissionRequest->id,
                    'reference_type' => PermissionRequest::class,
                    'read_at' => null,
                ]
            );
        }

        return $reviewers->count();
    }

    public function buildDateTime(string $date, ?string $time, bool $isEnd = false): ?Carbon
    {
        if ($time) {
            return Carbon::parse("{$date} {$time}");
        }

        return $isEnd ? Carbon::parse($date)->endOfDay() : Carbon::parse($date)->startOfDay();
    }

    public function activeSignataires(): Collection
    {
        return Signataire::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->orderByRaw('CASE WHEN ordre IS NULL THEN 1 ELSE 0 END')
            ->orderBy('ordre')
            ->orderBy('nom')
            ->get();
    }
}
