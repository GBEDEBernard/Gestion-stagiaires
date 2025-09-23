<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use Barryvdh\DomPDF\Facade\Pdf;

class AttestationController extends Controller
{
    public function show(Stage $stage)
    {
        $stage->load('etudiant', 'service', 'typestage');
        return view('admin.stages.attestation', compact('stage'));
    }

    public function download(Stage $stage)
    {
        $stage->load('etudiant', 'service', 'typestage');

        $pdf = Pdf::loadView('admin.stages.attestation_pdf', compact('stage'))
            ->setPaper('A4', 'portrait')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('attestation_'.$stage->etudiant->nom.'.pdf');
    }
}
