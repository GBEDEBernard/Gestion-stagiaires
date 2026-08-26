<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UrgentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminUrgentNotificationController extends Controller
{
    protected UrgentNotificationService $urgentService;

    public function __construct(UrgentNotificationService $urgentService)
    {
        $this->urgentService = $urgentService;
    }

    /**
     * Affiche la page de composition et l'historique des alertes urgentes.
     */
    public function index()
    {
        $targetOptions = $this->urgentService->getTargetOptions();
        $recentBatches = $this->urgentService->getRecentBatches(15);
        $totalActiveUsers = $this->urgentService->getAllActiveUsers()->count();

        return view('admin.notifications.urgent.create', compact('targetOptions', 'recentBatches', 'totalActiveUsers'));
    }

    /**
     * Diffuse une nouvelle alerte urgente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'url' => 'nullable|url|max:500',
            'attachment' => 'nullable|file|mimes:pdf|max:20480',
            'target_type' => 'required|string|in:all,employes,poste,stagiaires,typestage,domaine,individual',
            'target_value' => 'nullable|string|max:255',
            'individual_ids' => 'nullable|array',
            'individual_ids.*' => 'exists:users,id',
        ], [
            'title.required' => 'Le titre de l\'alerte est obligatoire.',
            'message.required' => 'Le message détaillé de l\'alerte est obligatoire.',
            'target_type.required' => 'Veuillez sélectionner un groupe cible.',
            'url.url' => 'Le lien d\'action doit être une URL valide (ex: https://...).',
            'attachment.mimes' => 'Le fichier attaché doit être un PDF.',
            'attachment.max' => 'Le fichier ne doit pas dépasser 20 Mo.',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('urgent-attachments', 'public');
            $attachment = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ];
        }

        $data = $validated;
        unset($data['attachment']);
        $data['attachment'] = $attachment;

        $result = $this->urgentService->broadcast($data, Auth::user());

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->route('admin.notifications.urgent.index')
            ->with('success', $result['message']);
    }

    /**
     * API: Compte en temps réel les destinataires d'un critère de ciblage.
     */
    public function countRecipients(Request $request)
    {
        $targetType = $request->input('target_type', 'all');
        $targetValue = $request->input('target_value');
        $individualIds = $request->input('individual_ids', []);

        if (is_string($individualIds)) {
            $individualIds = array_filter(explode(',', $individualIds));
        }

        $count = $this->urgentService->countRecipients($targetType, $targetValue, (array) $individualIds);

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * API: Détail des accusés de réception d'un lot d'alertes.
     */
    public function batchDetails(string $batchId)
    {
        $details = $this->urgentService->getBatchDetails($batchId);

        if (!$details) {
            return response()->json(['error' => 'Lot de notifications introuvable.'], 404);
        }

        return response()->json($details);
    }

    /**
     * Accuse réception d'une notification urgente par l'utilisateur connecté.
     */
    public function acknowledge(Request $request, $id)
    {
        $success = $this->urgentService->acknowledge($id, Auth::user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Accusé de réception enregistré.' : 'Notification introuvable ou déjà acquittée.',
            ]);
        }

        return redirect()->back()->with('success', 'Vous avez pris connaissance de l\'alerte.');
    }

    /**
     * Accuse réception de TOUTES les notifications urgentes en attente.
     */
    public function acknowledgeAll(Request $request)
    {
        $count = $this->urgentService->acknowledgeAll(Auth::user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => 'Toutes les alertes ont été acquittées.',
            ]);
        }

        return redirect()->back()->with('success', 'Toutes les alertes urgentes ont été acquittées.');
    }
}
