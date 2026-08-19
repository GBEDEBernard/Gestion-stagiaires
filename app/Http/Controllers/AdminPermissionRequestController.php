<?php

namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use App\Models\PermissionType;
use App\Models\User;
use App\Services\PermissionRequestService;
use Illuminate\Http\Request;

class AdminPermissionRequestController extends Controller
{
    public function __construct(protected PermissionRequestService $service) {}

    public function index(Request $request)
    {
        $query = PermissionRequest::with(['user', 'type', 'recipients.signataire', 'decider'])
            ->orderByDesc('created_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($typeId = $request->get('type_id')) {
            $query->where('permission_type_id', $typeId);
        }
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $requests = $query->paginate(15)->withQueryString();
        $types    = PermissionType::active()->get();
        $users    = User::with('personnel')
            ->whereHas('permissionRequests')
            ->get()
            ->sortBy('name')
            ->values();

        $stats = [
            'total'    => PermissionRequest::count(),
            'pending'  => PermissionRequest::where('status', 'pending')->count(),
            'approved' => PermissionRequest::where('status', 'approved')->count(),
            'rejected' => PermissionRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.permissions.index', compact('requests', 'types', 'users', 'stats'));
    }

    public function show(PermissionRequest $permission)
    {
        $permission->load(['type', 'user', 'recipients.signataire', 'decider']);

        return response()->json([
            'id'               => $permission->id,
            'user'             => $permission->user->name,
            'user_email'       => $permission->user->email,
            'type'             => $permission->type->name,
            'type_color'       => $permission->type->color,
            'status'           => $permission->status,
            'status_label'     => $permission->statusLabel(),
            'fields_data'      => $permission->fields_data,
            'fields_config'    => $permission->type->fields_config,
            'note'             => $permission->note,
            'decision_comment' => $permission->decision_comment,
            'decided_at'       => $permission->decided_at?->diffForHumans(),
            'decider'          => $permission->decider?->name,
            'created_at'       => $permission->created_at->format('d/m/Y H:i'),
            'recipients'       => $permission->recipients->map(fn($r) => [
                'nom'    => $r->signataire->nom,
                'poste'  => $r->signataire->poste,
                'status' => $r->statusLabel(),
                'state'  => $r->status,
            ]),
        ]);
    }

    public function decide(Request $request, PermissionRequest $permission)
    {
        abort_unless($permission->isPending(), 422, 'Cette demande a déjà été traitée.');

        $validated = $request->validate([
            'decision' => 'required|in:approve,reject',
            'comment'  => 'nullable|string|max:1000',
        ]);

        $this->service->decide($permission, auth()->user(), $validated['decision'], $validated['comment'] ?? null);

        return response()->json(['success' => true, 'message' => 'Décision enregistrée.']);
    }
}
