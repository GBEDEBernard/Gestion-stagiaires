<?php

namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use App\Models\Signataire;
use App\Services\PermissionRequestWorkflowService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PermissionRequestController extends Controller
{
    public function __construct(
        private readonly PermissionRequestWorkflowService $workflow
    ) {
    }

    public function index(HttpRequest $request)
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['etudiant', 'employe']), Response::HTTP_FORBIDDEN);
        $context = $this->workflow->resolveContext($user);

        $permissionRequests = PermissionRequest::query()
            ->with(['firstApprover', 'reviewer', 'stage.site', 'domaine'])
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(10);

        return view('permission-requests.index', [
            'permissionRequests' => $permissionRequests,
            'permissionContext' => $context,
            'typeOptions' => PermissionRequest::typeOptions(),
            'activeSignatairesCount' => $this->workflow->activeSignataires()->count(),
        ]);
    }

    public function store(HttpRequest $request)
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['etudiant', 'employe']), Response::HTTP_FORBIDDEN);
        $context = $this->workflow->resolveContext($user);

        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(PermissionRequest::typeOptions())),
            'request_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:2000',
            'intent' => 'nullable|in:draft,submit',
        ]);

        $permissionRequest = PermissionRequest::create([
            'user_id' => $user->id,
            'stage_id' => $context['stage']?->id,
            'domaine_id' => $context['domaine']?->id,
            'first_approver_id' => $context['approver']?->id,
            'type' => $validated['type'],
            'request_date' => $validated['request_date'],
            'starts_at' => $this->workflow->buildDateTime($validated['request_date'], $validated['start_time'] ?? null),
            'ends_at' => $this->workflow->buildDateTime($validated['request_date'], $validated['end_time'] ?? null, true),
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
        ]);

        $isDraft = ($validated['intent'] ?? 'submit') === 'draft';
        $permissionRequest = $this->workflow->submit($permissionRequest, $isDraft);

        $successMessage = match ($permissionRequest->status) {
            PermissionRequest::STATUS_DRAFT => 'Le brouillon de demande de permission a ete enregistre.',
            PermissionRequest::STATUS_UNDER_REVIEW => 'La demande de permission a ete envoyee au superviseur et a l\'administration.',
            PermissionRequest::STATUS_SENT => 'La demande a ete diffusee vers les signataires officiels.',
            PermissionRequest::STATUS_APPROVED => 'La demande a ete approuvee, en attente de diffusion.',
            default => 'La demande de permission a ete enregistree avec succes.',
        };

        return redirect()
            ->route('permission-requests.show', $permissionRequest)
            ->with('success', $successMessage);
    }

    public function show(PermissionRequest $permissionRequest)
    {
        Gate::authorize('viewPermissionRequest', $permissionRequest);

        $permissionRequest->loadMissing(['requester.domaine', 'stage.site', 'stage.supervisor', 'domaine', 'firstApprover', 'reviewer']);

        return view('permission-requests.show', [
            'permissionRequest' => $permissionRequest,
        ]);
    }

    public function downloadPdf(PermissionRequest $permissionRequest)
    {
        Gate::authorize('viewPermissionRequest', $permissionRequest);

        $pdfPath = $this->workflow->ensurePdf($permissionRequest);

        return response()->download(
            storage_path('app/' . $pdfPath),
            'demande-permission-' . $permissionRequest->id . '.pdf'
        );
    }

    public function reviewIndex(HttpRequest $request)
    {
        abort_unless(Gate::allows('reviewPermissionRequests'), Response::HTTP_FORBIDDEN);

        $user = $request->user();
        $query = PermissionRequest::query()
            ->with(['requester.domaine', 'stage.site', 'firstApprover', 'reviewer'])
            ->latest('submitted_at')
            ->latest('created_at');

        if (!$user->hasRole('admin')) {
            $query->where(function ($builder) use ($user) {
                $builder
                    ->where('first_approver_id', $user->id)
                    ->orWhere('reviewed_by_id', $user->id);
            });
        }

        return view('admin.permission-requests.index', [
            'permissionRequests' => $query->paginate(12),
            'activeSignatairesCount' => Signataire::query()->where('is_active', true)->whereNotNull('email')->count(),
        ]);
    }

    public function approve(PermissionRequest $permissionRequest, HttpRequest $request)
    {
        Gate::authorize('actOnPermissionRequest', $permissionRequest);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1500',
        ]);

        $permissionRequest = $this->workflow->approve(
            $permissionRequest,
            $request->user(),
            $validated['notes'] ?? null
        );

        return redirect()
            ->route('permission-requests.review.index')
            ->with('success', $permissionRequest->status === PermissionRequest::STATUS_SENT
                ? 'La demande a ete approuvee et envoyee aux signataires.'
                : 'La demande a ete approuvee.');
    }

    public function reject(PermissionRequest $permissionRequest, HttpRequest $request)
    {
        Gate::authorize('actOnPermissionRequest', $permissionRequest);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1500',
        ]);

        $this->workflow->reject(
            $permissionRequest,
            $request->user(),
            $validated['notes'] ?? null
        );

        return redirect()
            ->route('permission-requests.review.index')
            ->with('success', 'La demande de permission a ete refusee.');
    }
}
