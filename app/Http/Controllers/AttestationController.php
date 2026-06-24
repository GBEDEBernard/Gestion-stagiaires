<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Signataire;
use App\Models\Attestation;
use App\Models\User;
use App\Mail\AttestationSignerNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Mpdf\Mpdf;

class AttestationController extends Controller
{
    /**
     * Affiche l'attestation avec référence incrémentale annuelle
     */
    public function showStageAttestation(Stage $stage)
    {
        $stage->load([
            'etudiant.personnel',
            'domaine',
            'typestage',
            'attestation.signataires.user.personnel',
        ]);

        $attestation = $stage->attestation;
        if (!$attestation) {
            $reference = $this->generateReference();
            $attestation = $stage->attestation()->create([
                'typestage_id' => $stage->typestage?->id,
                'reference'    => $reference,
                'date_delivrance' => now(),
            ]);
        } else {
            $reference = $attestation->reference;
        }

        $eligibleUsers = User::role('admin')
            ->where('is_signer', true)
            ->permission('signer_attestation')
            ->with(['personnel.personnable'])
            ->leftJoin('personnels', 'personnels.id', '=', 'users.personnel_id')
            ->select('users.*')
            ->orderBy('personnels.nom')
            ->orderBy('personnels.prenom')
            ->get();

        $selectedSignataireIds = $attestation->signataires()
            ->pluck('user_id')
            ->filter()
            ->all();

        $signataires = $attestation->signataires()
            ->orderBy('pivot_ordre', 'asc')
            ->get();

        return view('admin.stages.attestation', compact(
            'stage', 'eligibleUsers', 'reference', 'attestation', 'selectedSignataireIds', 'signataires'
        ));
    }

    /**
     * Affiche la page de signature pour un signataire
     */
    public function showSignature($attestationId, $signerId, $token)
    {
        $attestation = Attestation::with(['stage.etudiant.personnel', 'signataires'])->findOrFail($attestationId);
        $signer = User::findOrFail($signerId);

        $expectedToken = hash_hmac('sha256', $signer->id . $attestation->id, config('app.key'));
        if (!hash_equals($expectedToken, $token)) {
            abort(403, 'Lien de signature invalide ou expiré.');
        }

        $signataire = $attestation->signataires()->where('user_id', $signer->id)->first();
        if (!$signataire) {
            abort(403, 'Vous n\'êtes pas autorisé à signer cette attestation.');
        }

        if ($signataire->pivot->signed_at) {
            return view('attestation.already-signed', compact('attestation', 'signer'));
        }

        return view('attestation.sign', compact('attestation', 'signer', 'token'));
    }

    /**
     * Traite la signature d'une attestation
     */
    public function processSignature(Request $request, $attestationId, $signerId, $token)
    {
        $request->validate([
            'signature_data' => 'required|string',
            'confirm'        => 'required|accepted',
        ]);

        $attestation = Attestation::findOrFail($attestationId);
        $signer      = User::findOrFail($signerId);

        $expectedToken = hash_hmac('sha256', $signer->id . $attestation->id, config('app.key'));
        if (!hash_equals($expectedToken, $token)) {
            return back()->with('error', 'Lien de signature invalide.');
        }

        $attestation->signataires()->updateExistingPivot($signer->id, [
            'signed_at'      => now(),
            'signature_data' => $request->signature_data,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        \App\Models\Activity::create([
            'user_id'     => $signer->id,
            'action'      => 'Signature attestation',
            'description' => "Signature de l'attestation pour {$attestation->stage->etudiant->personnel->nom}",
        ]);

        return redirect()->route('dashboard')->with('success', 'Attestation signée avec succès !');
    }

    /**
     * Générer la référence ATS incrémentale annuelle
     */
    protected function generateReference()
    {
        $currentYear = date('y');
        $currentMonth = date('m');

        $lastAttestation = Attestation::whereYear('created_at', Carbon::now()->year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAttestation) {
            preg_match('/ATS (\d+)_\d{2}\/\d{2}/', $lastAttestation->reference, $matches);
            if (!$matches) {
                preg_match('/ATS (\d+)_\d{2}/', $lastAttestation->reference, $matches);
            }
            $numero = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $numero = 1;
        }

        $numero = str_pad($numero, 2, '0', STR_PAD_LEFT);

        return "ATS {$numero}/{$currentMonth}-{$currentYear} / TFG / DG / DT-ISI / SD";
    }

    /**
     * Stocke les signataires choisis pour le stage
     */
    public function store(Request $request, Stage $stage)
    {
        $request->validate([
            'signataires.*.ordre'     => 'nullable|integer|min:1|max:10',
            'signataires.*.selected'  => 'nullable|boolean',
            'signataires.*.par_ordre' => 'nullable|boolean',
        ]);

        $selected = $request->input('signataires', []);

        $attestation = $stage->attestation ?? $stage->attestation()->create([
            'typestage_id'    => $stage->typestage?->id,
            'reference'       => $this->generateReference(),
            'date_delivrance' => now(),
        ]);

        $syncData      = [];
        $notifiedUsers = [];

        foreach ($selected as $userId => $data) {
            if (empty($data['selected'])) {
                continue;
            }

            $user = User::find($userId);
            if (!$user || !$user->isSigner() || !$user->hasRole('admin')) {
                continue;
            }

            $signataire = Signataire::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nom'          => $user->personnel?->full_name ?? $user->name,
                    'email'        => $user->getEmailForVerification(),
                    'poste'        => $user->personnel?->personnable?->poste ?? 'Signataire',
                    'sigle'        => $user->isDG() ? 'DG' : ($user->isDTA() ? 'DTA' : ($user->isDT() ? 'DT' : 'SIG')),
                    'ordre'        => false,
                    'peut_par_ordre' => !$user->isDG(),
                ]
            );

            $syncData[$signataire->id] = [
                'par_ordre'      => isset($data['par_ordre']) && $data['par_ordre'],
                'ordre'          => $signataire->peut_par_ordre && isset($data['ordre']) ? intval($data['ordre']) : null,
                'signed_at'      => null,
                'signature_data' => null,
                'notified_at'    => now(),
            ];

            $notifiedUsers[$user->id] = $user;
        }

        $attestation->signataires()->sync($syncData);

        foreach ($notifiedUsers as $user) {
            $emailToSend = $user->getEmailForVerification();

            if ($emailToSend) {
                try {
                    Mail::to($emailToSend)->send(new AttestationSignerNotificationMail($user, $stage, $attestation));

                    Log::info('Notification signature envoyée', [
                        'attestation'    => $attestation->reference,
                        'stagiaire'      => $stage->etudiant->personnel->nom,
                        'signataire_id'  => $user->id,
                        'signataire_nom' => $user->personnel?->full_name ?? $user->name,
                        'email_to_send'  => $emailToSend,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur envoi notification signature', [
                        'error'         => $e->getMessage(),
                        'attestation'   => $attestation->reference,
                        'signataire_id' => $user->id,
                        'email_to_send' => $emailToSend,
                    ]);
                }
            } else {
                Log::warning('Notification signature non envoyée (email vide)', [
                    'attestation'   => $attestation->reference,
                    'signataire_id' => $user->id,
                ]);
            }
        }

        return redirect(encrypted_route('stages.attestation.show', $stage))
            ->with('success', 'Signataires enregistrés et notifications envoyées.');
    }

    /**
     * Génère le PDF pour impression ou téléchargement
     *
     * Stratégie d'une seule page :
     * - Mpdf gère les marges (15 mm sur chaque côté)
     * - Le blade ne rajoute PAS de padding quand $isPdf est défini
     * - Le footer TFG est injecté via SetHTMLFooter() pour rester
     *   ancré en bas sans sortir du flux principal
     * - font-size réduit dans le blade pour que le contenu tienne
     *   dans 267 mm de hauteur utile (297 - 15 - 15)
     */
    public function generatePDF(Stage $stage, $type = 'download')
    {
        $stage->load([
            'etudiant.personnel',
            'domaine',
            'typestage',
            'attestation.signataires',
        ]);

        $attestation = $stage->attestation ?? $stage->attestation()->create([
            'typestage_id'    => $stage->typestage?->id,
            'reference'       => $this->generateReference(),
            'date_delivrance' => now(),
        ]);

        if ($type === 'download' && $attestation->download_count >= 6) {
            abort(403, 'Limite de téléchargement atteinte (6 maximum).');
        }

        $reference  = $attestation->reference;
        $signataires = $attestation->signataires()->orderBy('pivot_ordre')->get();

        foreach ($signataires as $signataire) {
            if (!$signataire->pivot) {
                $signataire->setRelation('pivot', (object)[
                    'par_ordre' => false,
                    'ordre'     => null,
                ]);
            }
        }

        // Préparer le logo en base64
        $logoPath    = public_path('images/TFGLOGO.png');
        $logoBase64  = base64_encode(file_get_contents($logoPath));
        $logoDataUri = 'data:image/png;base64,' . $logoBase64;
        $isPdf       = true;

        // Rendre le HTML (le blade n'inclut plus le pied de page en abs)
        $html = view('admin.stages.attestation', compact(
            'stage', 'signataires', 'reference', 'logoPath', 'logoDataUri', 'isPdf'
        ))->render();

        // -------------------------------------------------------
        // Mpdf : marges gèrent l'espace, pas le padding du container
        // margin_bottom = 20 mm pour laisser de la place au footer
        // -------------------------------------------------------
        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'default_font'  => 'dejavuserif',
        ]);

        // Désactiver le saut de page automatique
        $mpdf->SetAutoPageBreak(false);

        ini_set('pcre.backtrack_limit', '4000000');
        ini_set('pcre.jit', '0');

        $mpdf->WriteHTML($html);

        $fileName   = 'attestation_' . ($stage->etudiant->personnel->nom ?? 'stagiaire') . '.pdf';
        $pdfContent = $mpdf->Output($fileName, \Mpdf\Output\Destination::STRING_RETURN);

        if ($type === 'print') {
            return response($pdfContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        $attestation->increment('download_count');

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
