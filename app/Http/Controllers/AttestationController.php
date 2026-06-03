<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Signataire;
use App\Models\Attestation;
use App\Models\User;
use App\Mail\AttestationSignerNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Mpdf\Mpdf;

class AttestationController extends Controller
{
    /**
     * Affiche l'attestation avec référence incrémentale annuelle
     */
    public function show(Stage $stage)
    {
        $stage->load([
            'etudiant.personnel',
            'service',
            'typestage',
            'attestation.signataires.user.personnel',
        ]);

        $attestation = $stage->attestation;
        if (!$attestation) {
            $reference = $this->generateReference();
            $attestation = $stage->attestation()->create([
                'typestage_id' => $stage->typestage?->id,
                'reference' => $reference,
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

        return view('admin.stages.attestation', compact('stage', 'eligibleUsers', 'reference', 'attestation', 'selectedSignataireIds', 'signataires'));
    }

    /**
     * Générer la référence ATS incrémentale annuelle
     */
    protected function generateReference()
    {
        $currentYear = date('y');

        $lastAttestation = Attestation::whereYear('created_at', Carbon::now()->year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAttestation) {
            preg_match('/ATS (\d+)_\d{2}/', $lastAttestation->reference, $matches);
            $numero = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $numero = 1;
        }

        $numero = str_pad($numero, 2, '0', STR_PAD_LEFT);

        return "ATS {$numero}_{$currentYear} / TFG / DG / DT-ISI / SD";
    }

    /**
     * Stocke les signataires choisis pour le stage
     */
    public function store(Request $request, Stage $stage)
    {
        $request->validate([
            'signataires.*.ordre' => 'nullable|integer|min:1|max:10',
            'signataires.*.selected' => 'nullable|boolean',
            'signataires.*.par_ordre' => 'nullable|boolean',
        ]);

        $selected = $request->input('signataires', []);

        $attestation = $stage->attestation ?? $stage->attestation()->create([
            'typestage_id' => $stage->typestage?->id,
            'reference' => $this->generateReference(),
            'date_delivrance' => now(),
        ]);

        $syncData = [];
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
                    'nom' => $user->personnel?->full_name ?? $user->name,
                    'email' => $user->getEmailForVerification(),
                    'poste' => $user->personnel?->personnable?->poste ?? 'Signataire',
                    'sigle' => $user->isDG() ? 'DG' : ($user->isDTA() ? 'DTA' : ($user->isDT() ? 'DT' : 'SIG')),
                    'ordre' => false,
                    'peut_par_ordre' => !$user->isDG(),
                ]
            );

            $syncData[$signataire->id] = [
                'par_ordre' => isset($data['par_ordre']) && $data['par_ordre'],
                'ordre' => $signataire->peut_par_ordre && isset($data['ordre']) ? intval($data['ordre']) : null,
            ];

            $notifiedUsers[$user->id] = $user;
        }

        $attestation->signataires()->sync($syncData);

        foreach ($notifiedUsers as $user) {
            if ($user->getEmailForVerification()) {
                Mail::to($user->getEmailForVerification())
                    ->queue(new AttestationSignerNotificationMail($user, $stage, $attestation));
            }
        }

        return redirect(encrypted_route('stages.attestation.show', $stage))
            ->with('success', 'Signataires enregistrés. Vous pouvez maintenant générer l’attestation.');
    }

    /**
     * Génère le PDF pour impression ou téléchargement
     */
    public function generatePDF(Stage $stage, $type = 'download')
    {
        $stage->load([
            'etudiant.personnel',
            'service',
            'typestage',
            'attestation.signataires'
        ]);

        $attestation = $stage->attestation ?? $stage->attestation()->create([
            'typestage_id' => $stage->typestage?->id,
            'reference' => $this->generateReference(),
            'date_delivrance' => now(),
        ]);

        $reference = $attestation->reference;
        $signataires = $attestation->signataires()->orderBy('pivot_ordre')->get();

        // Assurer que chaque signataire a un attribut pivot par défaut
        foreach ($signataires as $signataire) {
            if (!$signataire->pivot) {
                $signataire->setRelation('pivot', (object)[
                    'par_ordre' => false,
                    'ordre' => null
                ]);
            }
        }

        // Génération HTML – la vue doit aussi être corrigée (cf. ci-dessous)
        $html = view('admin.stages.attestation_pdf', compact('stage', 'signataires', 'reference'))->render();

        // Création du PDF avec mPDF
        $mpdf = new Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);

        $fileName = 'attestation_' . ($stage->etudiant->personnel->nom ?? 'stagiaire') . '.pdf';

        if ($type === 'print') {
            return $mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE);
        }

        return $mpdf->Output($fileName, \Mpdf\Output\Destination::DOWNLOAD);
    }
}
