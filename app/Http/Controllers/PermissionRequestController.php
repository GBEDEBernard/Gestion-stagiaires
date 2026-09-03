<?php

namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use App\Models\PermissionType;
use App\Models\Signataire;
use App\Services\PermissionRequestService;
use Illuminate\Http\Request;

class PermissionRequestController extends Controller
{
    public function __construct(protected PermissionRequestService $service) {}

    public function index(Request $request)
    {
        $user     = auth()->user();
        $etudiant = $user->etudiant;

        $query = PermissionRequest::with(['type', 'recipients.signataire', 'decider'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        // Filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($typeId = $request->get('type_id')) {
            $query->where('permission_type_id', $typeId);
        }
        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $requests   = $query->paginate(10)->withQueryString();
        $types      = PermissionType::active()->get();
        $signataires = Signataire::orderBy('nom')->get();

        $stats = [
            'total'    => PermissionRequest::where('user_id', $user->id)->count(),
            'pending'  => PermissionRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => PermissionRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => PermissionRequest::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('permissions.index', compact('requests', 'types', 'signataires', 'stats'));
    }

    public function store(Request $request)
    {
        $type = PermissionType::findOrFail($request->input('permission_type_id'));

        // Build validation rules from type's field config
        $rules = ['permission_type_id' => 'required|exists:permission_types,id'];
        foreach ($type->fields_config as $field) {
            $rule = $field['required'] ? 'required' : 'nullable';
            $rule .= match($field['type']) {
                'date'     => '|date',
                'time'     => '|date_format:H:i',
                'textarea' => '|string|max:2000',
                default    => '|string',
            };
            $rules["fields_data.{$field['key']}"] = $rule;
        }
        $rules['signataire_ids']   = 'required|array|min:1';
        $rules['signataire_ids.*'] = 'exists:signataires,id';
        $rules['note']             = 'nullable|string|max:1000';

        $validated = $request->validate($rules);

        $this->service->create(auth()->user(), [
            'permission_type_id' => $validated['permission_type_id'],
            'fields_data'        => $validated['fields_data'],
            'note'               => $validated['note'] ?? null,
            'signataire_ids'     => $validated['signataire_ids'],
        ]);

        return back()->with('success', 'Votre demande de permission a été soumise avec succès.');
    }

    public function cancel(PermissionRequest $permission)
    {
        abort_unless(auth()->id() === $permission->user_id, 403);
        abort_unless($permission->isPending(), 403, 'Cette demande ne peut plus être annulée.');

        $this->service->cancel($permission);

        return back()->with('success', 'Demande annulée.');
    }

    public function show(PermissionRequest $permission)
    {
        abort_unless(auth()->id() === $permission->user_id, 403);

        $permission->load(['type', 'recipients.signataire', 'decider']);

        return response()->json([
            'id'               => $permission->id,
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
}
