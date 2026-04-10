<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Signataire;
use App\Models\Attestation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mpdf\Mpdf;

class AttestationController extends Controller
{
    /**
     * Affiche l'attestation avec référence incrémentale annuelle
     */
    public function show(Stage $stage)
    {
        // Charger relations
        $stage->load('etudiant', 'service', 'typestage', 'attestation.signataires');

        // Attestation existante ou création
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

        // Récupérer uniquement les signataires sélectionnés pour cette attestation
        // Tri par ordre si défini
        $signataires = $attestation->signataires()
            ->orderBy('pivot_ordre', 'asc')
            ->get();

        return view('admin.stages.attestation', compact('stage', 'signataires', 'reference'));
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

        return "ATS {$numero}_{$currentYear} / TFG / DG / DT / SD";
    }

    /**
     * Stocke les signataires choisis pour le stage
     */
    public function store(Request $request, Stage $stage)
    {
        $request->validate([
            'signataires.*.ordre' => 'nullable|integer|min:1|max:10',
            'signataires.*.selected' => 'nullable|boolean',
        ]);

        $selected = $request->input('signataires', []);

        $attestation = $stage->attestation ?? $stage->attestation()->create([
            'typestage_id' => $stage->typestage?->id,
            'reference' => $this->generateReference(),
            'date_delivrance' => now(),
        ]);

        $syncData = [];
        foreach ($selected as $signataire_id => $data) {
            $signataire = Signataire::find($signataire_id);
            if (!$signataire) continue;

            $syncData[$signataire_id] = [
                'par_ordre' => $signataire->peut_par_ordre && isset($data['ordre']),
                'ordre' => $data['ordre'] ?? null
            ];
        }

        $attestation->signataires()->sync($syncData);

        return redirect(encrypted_route('stages.attestation.show', $stage))
            ->with('success', 'Signataires enregistrés. Vous pouvez maintenant générer l’attestation.');
    }

    /**
     * Génère le PDF pour impression ou téléchargement
     */

    public function generatePDF(Stage $stage, $type = 'download')
    {
        $stage->load('etudiant', 'service', 'typestage', 'attestation.signataires');

        $attestation = $stage->attestation ?? $stage->attestation()->create([
            'typestage_id' => $stage->typestage?->id,
            'reference' => $this->generateReference(),
            'date_delivrance' => now(),
        ]);

        $reference = $attestation->reference;
        $signataires = $attestation->signataires()->orderBy('pivot_ordre')->get();

        foreach ($signataires as $signataire) {
            if (!$signataire->pivot) {
                $signataire->setRelation('pivot', (object)[
                    'par_ordre' => false,
                    'ordre' => null
                ]);
            }
        }

        // Génération HTML
        $html = view('admin.stages.attestation_pdf', compact('stage', 'signataires', 'reference'))->render();

        // Création du PDF avec mPDF
        $mpdf = new Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);

        if ($type === 'print') {
            // Affiche directement dans le navigateur pour impression
            return $mpdf->Output('attestation_' . $stage->etudiant->nom . '.pdf', \Mpdf\Output\Destination::INLINE);
        }

        // Pour téléchargement
        return $mpdf->Output('attestation_' . $stage->etudiant->nom . '.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    }
}
