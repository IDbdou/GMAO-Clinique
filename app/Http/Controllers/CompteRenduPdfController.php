<?php

namespace App\Http\Controllers;

use App\Models\CompteRendu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CompteRenduPdfController extends Controller
{
    public function download(Request $request, CompteRendu $compteRendu)
    {
        $compteRendu->load(['intervention.equipement', 'intervention.service', 'technicien']);

        $pdf = Pdf::loadView('pdf.compte-rendu', [
            'cr' => $compteRendu,
            'intervention' => $compteRendu->intervention,
            'equipement' => $compteRendu->intervention?->equipement,
            'service' => $compteRendu->intervention?->service,
            'technicien' => $compteRendu->technicien,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("compte-rendu-CR-{$compteRendu->id}.pdf");
    }
}
