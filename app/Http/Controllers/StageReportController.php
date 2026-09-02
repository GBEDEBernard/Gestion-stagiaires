<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Services\StageReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class StageReportController extends Controller
{
    public function __construct(
        protected StageReportService $reportService
    ) {}

    /**
     * Rapport de stage détaillé : assiduité, anomalies, permissions.
     * GET /admin/stages/{stage}/rapport
     */
    public function show(Request $request, Stage $stage)
    {
        $this->authorizeView($request, $stage);

        $report = $this->reportService->build($stage->load('etudiant.user', 'site', 'typestage', 'domaine'));

        // Trois niveaux de divulgation des anomalies. Le détail nomme des faits
        // parfois accusatoires — il reste un choix explicite, jamais le défaut.
        $disclosure = $request->query('anomalies', 'count');
        if (!in_array($disclosure, ['count', 'grouped', 'detailed'], true)) {
            $disclosure = 'count';
        }

        return view('admin.stages.rapport', [
            'stage'      => $stage,
            'report'     => $report,
            'disclosure' => $disclosure,
        ]);
    }

    /**
     * Rapport au format PDF, sur papier à en-tête TFG.
     * GET /admin/stages/{stage}/rapport/pdf
     */
    public function pdf(Request $request, Stage $stage, string $type = 'download')
    {
        $this->authorizeView($request, $stage);

        $stage->load('etudiant.personnel', 'site', 'typestage', 'domaine', 'evaluation.scores');

        $report     = $this->reportService->forDocument($stage);
        $evaluation = $stage->evaluation;

        // Le niveau de divulgation est celui retenu à la finalisation : le
        // document remis ne doit pas changer de contenu au gré de l'URL.
        $disclosure = $evaluation?->isFinalized()
            ? ($evaluation->anomaly_disclosure ?: 'count')
            : 'count';

        $logo = public_path('images/TFGLOGO.png');
        $logoDataUri = is_file($logo)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo))
            : null;

        $html = view('admin.stages.rapport_pdf', [
            'stage'       => $stage,
            'report'      => $report,
            'evaluation'  => $evaluation,
            'disclosure'  => $disclosure,
            'logoDataUri' => $logoDataUri,
        ])->render();

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'default_font'  => 'dejavuserif',
        ]);

        // Filigrane natif plutôt qu'un bloc positionné : le rapport peut tenir
        // sur plusieurs pages, et mPDF le répète alors sur chacune.
        if (is_file($logo)) {
            $mpdf->SetWatermarkImage($logo, 0.06, [95, 95], 'P');
            $mpdf->showWatermarkImage = true;
        }

        ini_set('pcre.backtrack_limit', '4000000');
        ini_set('pcre.jit', '0');

        $mpdf->WriteHTML($html);

        $nom      = $stage->etudiant?->personnel?->nom ?? 'stagiaire';
        $fileName = 'rapport_stage_' . Str::slug($nom) . '.pdf';
        $content  = $mpdf->Output($fileName, Destination::STRING_RETURN);

        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => ($type === 'print' ? 'inline' : 'attachment')
                . '; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Le rapport reste interne : il est remis au stagiaire en main propre, sous
     * forme de PDF, et non consultable en ligne par lui.
     *
     * Un accès stagiaire après finalisation avait été envisagé, mais il ne
     * pouvait fonctionner que dans une fenêtre étroite — stage encore en cours
     * et pointage du jour effectué — car EnsureAccountActive désactive le compte
     * dès la fin du stage. Un accès qui marche pour quelques-uns seulement vaut
     * moins qu'une règle claire.
     */
    protected function authorizeView(Request $request, Stage $stage): void
    {
        $user = $request->user();

        if ($user->hasRole('admin') || $user->can('presence.admin')) {
            return;
        }

        abort(403, "Ce rapport n'est pas consultable en ligne.");
    }
}
