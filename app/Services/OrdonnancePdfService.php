<?php

namespace App\Services;

use App\Models\Ordonnance;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service pour la génération de PDF d'ordonnances.
 */
class OrdonnancePdfService
{
    /**
     * Générer une réponse HTTP PDF (inline stream) pour une ordonnance donnée.
     *
     * @param Ordonnance $ordonnance
     * @return Response
     */
    public function generate(Ordonnance $ordonnance): Response
    {
        $pdf = Pdf::loadView('pdf.ordonnance', ['ordonnance' => $ordonnance])
            ->setPaper('a4', 'portrait');

        $filename = 'ordonnance-' . $ordonnance->id . '-' . $ordonnance->date_emission->format('Y-m-d') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
