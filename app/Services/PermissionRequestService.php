<?php

namespace App\Services;

use App\Mail\PermissionDecisionMail;
use App\Mail\PermissionRequestNotificationMail;
use App\Models\AppNotification;
use App\Models\PermissionRequest;
use App\Models\PermissionRequestRecipient;
use App\Models\PermissionType;
use App\Models\Signataire;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PermissionRequestService
{
    /**
     * Create a new permission request and notify selected signataires.
     */
    public function create(User $user, array $data): PermissionRequest
    {
        return DB::transaction(function () use ($user, $data) {
            $etudiant = $user->etudiant;

            $request = PermissionRequest::create([
                'user_id'            => $user->id,
                'etudiant_id'        => $etudiant?->id,
                'permission_type_id' => $data['permission_type_id'],
                'fields_data'        => $data['fields_data'],
                'note'               => $data['note'] ?? null,
                'status'             => 'pending',
            ]);

            // Attach recipients (signataires)
            $signataires = Signataire::whereIn('id', $data['signataire_ids'])->get();

            foreach ($signataires as $signataire) {
                PermissionRequestRecipient::create([
                    'permission_request_id' => $request->id,
                    'signataire_id'         => $signataire->id,
                    'status'                => 'pending',
                    'notified_at'           => now(),
                ]);

                // Send email if signataire has an email
                if ($signataire->email) {
                    Mail::to($signataire->email)
                        ->queue(new PermissionRequestNotificationMail($request->load('type', 'user'), $signataire));
                }
            }

            // Notify admins in-app — join `personnels` so we can order by name columns
            $admins = User::role('admin')
                ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
                ->orderBy('personnels.nom')
                ->orderBy('personnels.prenom')
                ->select('users.*')
                ->get();
            foreach ($admins as $admin) {
                $this->notifyInApp($admin, [
                    'type'    => 'permission_request',
                    'title'   => 'Nouvelle demande de permission',
                    'message' => "{$user->name} a soumis une demande : {$request->type->name}",
                    'icon'    => 'shield',
                    'color'   => 'blue',
                    'url'     => route('admin.permissions.index'),
                    'ref_id'  => $request->id,
                    'ref_type' => PermissionRequest::class,
                ]);
            }

            return $request;
        });
    }

    /**
     * Validate or reject a request. First action wins.
     */
    public function decide(PermissionRequest $request, User $decider, string $decision, ?string $comment = null): PermissionRequest
    {
        if (!$request->isPending()) {
            return $request;
        }

        return DB::transaction(function () use ($request, $decider, $decision, $comment) {
            $status = $decision === 'approve' ? 'approved' : 'rejected';

            $request->update([
                'status'           => $status,
                'decided_by'       => $decider->id,
                'decided_at'       => now(),
                'decision_comment' => $comment,
            ]);

            // Find the acting recipient (if decider matches a signataire by email)
            $actingRecipient = null;
            foreach ($request->recipients as $recipient) {
                if ($recipient->signataire->email === $decider->email) {
                    $actingRecipient = $recipient;
                    break;
                }
            }

            // Mark acting recipient
            if ($actingRecipient) {
                $actingRecipient->update([
                    'status'    => $status === 'approved' ? 'validated' : 'rejected',
                    'action_at' => now(),
                    'comment'   => $comment,
                ]);
            }

            // Mark remaining recipients as skipped
            $request->recipients()
                ->where('status', 'pending')
                ->when($actingRecipient, fn($q) => $q->where('id', '<>', $actingRecipient->id))
                ->update([
                    'status'    => 'skipped',
                    'action_at' => now(),
                    'comment'   => "Décision déjà prise par {$decider->name}",
                ]);

            // Notify signataires who were skipped
            $skippedRecipients = $request->recipients()->where('status', 'skipped')->with('signataire')->get();
            foreach ($skippedRecipients as $recipient) {
                if ($recipient->signataire->email) {
                    // Could send a skipped email here if needed
                }
            }

            // Notify the requesting user
            $this->notifyInApp($request->user, [
                'type'    => 'permission_decision',
                'title'   => $status === 'approved' ? 'Permission approuvée ✓' : 'Permission refusée',
                'message' => $status === 'approved'
                    ? "Votre demande de {$request->type->name} a été approuvée par {$decider->name}."
                    : "Votre demande de {$request->type->name} a été refusée par {$decider->name}.",
                'icon'    => $status === 'approved' ? 'check-circle' : 'x-circle',
                'color'   => $status === 'approved' ? 'emerald' : 'red',
                'url'     => route('permissions.index'),
                'ref_id'  => $request->id,
                'ref_type' => PermissionRequest::class,
            ]);

            // Send decision email to user
            Mail::to($request->user->email)
                ->queue(new PermissionDecisionMail($request->load('type', 'decider'), $status));

            return $request->fresh();
        });
    }

    /**
     * Cancel a pending request (by the owner).
     */
    public function cancel(PermissionRequest $request): PermissionRequest
    {
        $request->update(['status' => 'cancelled']);
        return $request;
    }

    private function notifyInApp(User $user, array $data): void
    {
        $uniqueId = $data['type'] . '_' . $data['ref_id'] . '_' . $user->id;

        AppNotification::updateOrCreate(
            ['unique_id' => $uniqueId, 'user_id' => $user->id],
            [
                'type'           => $data['type'],
                'title'          => $data['title'],
                'message'        => $data['message'],
                'icon'           => $data['icon'],
                'color'          => $data['color'],
                'reference_id'   => $data['ref_id'],
                'reference_type' => $data['ref_type'],
                'url'            => $data['url'],
                'read_at'        => null,
            ]
        );
    }
}
